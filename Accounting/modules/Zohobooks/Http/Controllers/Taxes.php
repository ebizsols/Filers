<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Jobs\Setting\CreateTax;
use App\Jobs\Setting\UpdateTax;
use App\Models\Setting\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Taxes extends Controller
{
    use ZohoBooksRemote, ZohoBooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-settings-taxes')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-settings-taxes')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-settings-taxes')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-settings-taxes')->only('destroy');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function count()
    {
        $success = true;
        $error   = false;
        $message = null;
        $pages   = [];

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->settings->taxes->getTotal();

        if ($total>0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.taxes.sync', $i),
                ];
            }
        }

        Cache::set('zohobooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('zohobooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('zohobooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error   = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'taxes';

        $html = view('zohobooks::partial.sync', compact('type', 'total'))->render();

        return response()->json([
            'success' => $success,
            'error'   => $error,
            'message' => $message,
            'count'   => $total,
            'pages'   => $pages,
            'html'    => $html,
        ]);
    }

    public function sync()
    {
        $steps = [];

        $taxes = $this->getClient()->settings->taxes->getList();

        foreach ($taxes as $tax) {

            $cached[$tax->tax_id] = $this->prepareTaxData($tax);

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type'  => trans_choice('zohobooks::general.types.taxes', 1),
                        'value' => $tax->tax_name,
                    ]
                ),
                'url'  => route('zohobooks.sync.taxes.store'),
                'id'   => $tax->tax_id,
            ];
        }

        if (isset($cached)) {
            Cache::set('zohobooks_taxes_' . company_id(), $cached, Date::now()->addHours(6));
        }

        return response()->json([
            'errors'  => false,
            'success' => true,
            'steps'   => $steps,
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
        $taxes = Cache::get('zohobooks_taxes_' . company_id());

        $tax = $taxes[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $ak_tax = Tax::where('name', $tax['name'])->first();

        try {
            if ($ak_tax) {
                $ak_tax = $this->dispatch(new UpdateTax($ak_tax, $tax));
            } else {
                $ak_tax = $this->dispatch(new CreateTax($tax));
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
                'type' => trans_choice('zohobooks::general.types.taxes', 2)
            ]);

            setting()->set('zohobooks.last_check', $timestamp);
            setting()->save();

            Cache::set('zohobooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
