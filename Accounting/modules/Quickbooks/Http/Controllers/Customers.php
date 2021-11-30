<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Jobs\Common\CreateContact;
use App\Jobs\Common\UpdateContact;
use App\Models\Common\Contact;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\Quickbooks\Traits\QuickbooksTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class Customers extends Controller
{
    use QuickbooksRemote, QuickbooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-sales-customers')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-sales-customers')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-sales-customers')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-sales-customers')->only('destroy');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function count()
    {
        $success = true;
        $error = false;
        $message = null;
        $pages = [];

        Cache::set('quickbooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getCustomersCount();

        if ($total > 0) {

            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.customers.sync', $i),
                ];
            }
        }

        Cache::set('quickbooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('quickbooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('quickbooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'customers';

        $html = view('quickbooks::partial.sync', compact('type', 'total'))->render();

        return response()->json([
            'success' => $success,
            'error' => $error,
            'message' => $message,
            'count' => $total,
            'pages' => $pages,
            'html' => $html,
        ]);
    }

    public function sync()
    {
        $steps = [];

        $customer = $this->getCustomers();

        foreach ($customer as $customer) {

            $cached[$customer->Id] = $customer;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type' => trans_choice('quickbooks::general.types.customers', 1),
                        'value' => $customer->DisplayName,
                    ]
                ),
                'url' => route('quickbooks.sync.customers.store'),
                'id' => $customer->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_customers_' . company_id(), $cached, Date::now()->addHours(6));
        }

        return response()->json([
            'errors' => false,
            'success' => true,
            'steps' => $steps,
        ]);
    }

    /**
     * Enable the specified resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function store(Request $request)
    {
        $customers = Cache::get('quickbooks_customers_' . company_id());

        $contact = $customers[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $ak_customer_data = $this->prepareContactData($contact,'customer');

        $ak_customer = Contact::where('reference', 'quickbooks id: ' . $contact->Id)->first();

        try {
            if (empty($ak_customer)) {
                $ak_customer = $this->dispatch(new CreateContact($ak_customer_data));
            } else {
                $ak_customer = $this->dispatch(new UpdateContact($ak_customer, $ak_customer_data));
            }
        } catch (\Exception $e) {
            report($e);
        }

        $syncCount = Cache::get('quickbooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('quickbooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('quickbooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('quickbooks::general.finished', [
                'type' => trans_choice('quickbooks::general.types.customers', 2)
            ]);

            setting()->set('quickbooks.last_check', $timestamp);
            setting()->save();

            Cache::set('quickbooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
