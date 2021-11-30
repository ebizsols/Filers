<?php

namespace Modules\Quickbooks\Http\Controllers;

use Akaunting\Module\Routing\Controller;
use App\Models\Module\Module;
use Modules\Quickbooks\Http\Requests\SettingsRequest;
use Illuminate\Support\Facades\Cache;

class Settings extends Controller
{
    public $cloud_host = 'app.akaunting.com';

    public function edit()
    {
        $sync_actions = [
            'coa' => [
                'route' => 'quickbooks.sync.coa',
                'permission' => 'create-double-entry-chart-of-accounts',
            ],
            'taxes' => [
                'route' => 'quickbooks.sync.taxes',
                'permission' => 'create-settings-taxes',
            ],
            'products' => [
                'route' => 'quickbooks.sync.products',
                'permission' => 'create-common-items',
            ],
            'customers' => [
                'route' => 'quickbooks.sync.customers',
                'permission' => 'create-sales-customers',
            ],
            'vendors' => [
                'route' => 'quickbooks.sync.vendors',
                'permission' => 'create-purchases-vendors',
            ],
            'invoices' => [
                'route' => 'quickbooks.sync.invoices',
                'permission' => 'create-sales-invoices',
            ],
            'bills' => [
                'route' => 'quickbooks.sync.bills',
                'permission' => 'create-purchases-bills',
            ],
        ];

        $double_entry_enabled = Module::alias('double-entry')->enabled()->first();

        if (!$double_entry_enabled) {
            unset($sync_actions['coa']);
        }

        $is_cloud = (request()->getHost() == $this->cloud_host);

        return view("quickbooks::edit", compact('sync_actions', 'double_entry_enabled', 'is_cloud'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function update(SettingsRequest $request)
    {
        if ($request->getHost() == $this->cloud_host) {
            return redirect()->route('quickbooks.settings.edit');
        }

        setting()->set('quickbooks.client_id', $request['client_id']);
        setting()->set('quickbooks.client_secret', $request['client_secret']);

        if(is_null(setting('quickbooks.environment'))) {
            setting()->set('quickbooks.environment', 'production');
        }
//        setting()->set('quickbooks.environment', $request['environment']);
        setting()->save();

        if (config('setting.cache.enabled')) {
            Cache::forget(setting()->getCacheKey());
        }

        $message = trans(
            'messages.success.updated',
            [
                'type' => trans('quickbooks::general.name')
            ]
        );

        flash($message)->success();

        return redirect()->route('quickbooks.settings.edit');
    }
}
