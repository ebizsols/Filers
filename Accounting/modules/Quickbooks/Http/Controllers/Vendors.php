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

class Vendors extends Controller
{
    use QuickbooksRemote, QuickbooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-purchases-vendors')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-purchases-vendors')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-purchases-vendors')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-purchases-vendors')->only('destroy');
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

        $total = $this->getVendorsCount();

        if ($total > 0) {

            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.vendors.sync', $i),
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

        $type = 'vendors';

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

        $vendors = $this->getVendors();

        foreach ($vendors as $vendor) {

            $cached[$vendor->Id] = $vendor;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type' => trans_choice('quickbooks::general.types.vendors', 1),
                        'value' => $vendor->DisplayName,
                    ]
                ),
                'url' => route('quickbooks.sync.vendors.store'),
                'id' => $vendor->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_vendors_' . company_id(), $cached, Date::now()->addHours(6));
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
        $vendors = Cache::get('quickbooks_vendors_' . company_id());

        $vendor = $vendors[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $ak_contact_data = $this->prepareContactData($vendor,'vendor');

        $ak_contact = Contact::where('reference', 'quickbooks id: ' . $vendor->Id)->first();

        try {
            if (empty($ak_contact)) {
                $ak_contact = $this->dispatch(new CreateContact($ak_contact_data));
            } else {
                $ak_contact = $this->dispatch(new UpdateContact($ak_contact, $ak_contact_data));
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
                'type' => trans_choice('quickbooks::general.types.vendors', 2)
            ]);

            setting()->set('quickbooks.last_check', $timestamp);
            setting()->save();

            Cache::set('quickbooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
