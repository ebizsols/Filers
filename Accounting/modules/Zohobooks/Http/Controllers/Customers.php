<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Jobs\Common\CreateContact;
use App\Jobs\Common\UpdateContact;
use App\Models\Common\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Customers extends Controller
{
    use ZohoBooksRemote, ZohoBooksTransformer;

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

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->contacts->getTotal();

        if ($total > 0) {

            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.customers.sync', $i),
                ];
            }
        }

        Cache::set('zohobooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('zohobooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('zohobooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'customers';

        $html = view('zohobooks::partial.sync', compact('type', 'total'))->render();

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

        $customers = $this->getClient()->contacts->getList();

        foreach ($customers as $customer) {

            $cached[$customer->contact_id] = $this->prepareContactData($customer, $customer->contact_type);

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type' => trans_choice('zohobooks::general.types.customers', 1),
                        'value' => $customer->contact_name,
                    ]
                ),
                'url' => route('zohobooks.sync.customers.store'),
                'id' => $customer->contact_id,
            ];
        }

        if (isset($cached)) {
            Cache::set('zohobooks_customers_' . company_id(), $cached, Date::now()->addHours(6));
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
        $customers = Cache::get('zohobooks_customers_' . company_id());

        $contact = $customers[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $ak_customer = Contact::where('reference', $contact['reference'])->first();

        try {
            if (empty($ak_customer)) {
                $ak_customer = $this->dispatch(new CreateContact($contact));
            } else {
                $ak_customer = $this->dispatch(new UpdateContact($ak_customer, $contact));
            }
        } catch (\Exception $e) {
            report($e);
        }

        $syncCount = Cache::get('zohobooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('zohobooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('zohobooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('zohobooks::general.finished', [
                'type' => trans_choice('zohobooks::general.types.customers', 2)
            ]);

            setting()->set('zohobooks.last_check', $timestamp);
            setting()->save();

            Cache::set('zohobooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
