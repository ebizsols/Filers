<?php

namespace Modules\Zohobooks\Http\Controllers;

use Akaunting\Module\Routing\Controller;
use App\Models\Module\Module;
use Illuminate\Support\Facades\Cache;
use Modules\Zohobooks\Http\Requests\SettingsRequest;

class Settings extends Controller
{
    public $cloud_host = 'app.akaunting.com';

    public function edit()
    {
        $sync_actions = [
            'coa' => [
                'route' => 'zohobooks.sync.coa',
                'permission' => 'create-double-entry-chart-of-accounts',
            ],
            'taxes' => [
                'route' => 'zohobooks.sync.taxes',
                'permission' => 'create-settings-taxes',
            ],
            'products' => [
                'route' => 'zohobooks.sync.products',
                'permission' => 'create-common-items',
            ],
            'customers' => [
                'route' => 'zohobooks.sync.customers',
                'permission' => 'create-sales-customers',
            ],
            'invoices' => [
                'route' => 'zohobooks.sync.invoices',
                'permission' => 'create-sales-invoices',
            ],
            'bills' => [
                'route' => 'zohobooks.sync.bills',
                'permission' => 'create-purchases-bills',
            ],
        ];

        $double_entry_enabled = Module::alias('double-entry')->enabled()->first();

        if (!$double_entry_enabled) {
            unset($sync_actions['coa']);
        }

        $is_cloud = (request()->getHost() == $this->cloud_host);

        return view("zohobooks::edit", compact('sync_actions', 'double_entry_enabled', 'is_cloud'));
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
        if ($request->getHost() != $this->cloud_host) {
            setting()->set('zohobooks.client_id', $request['client_id']);
            setting()->set('zohobooks.client_secret', $request['client_secret']);
        }

        setting()->set('zohobooks.organization_id', $request['organization_id']);

        setting()->save();

        if (config('setting.cache.enabled')) {
            Cache::forget(setting()->getCacheKey());
        }

        $message = trans(
            'messages.success.updated',
            [
                'type' => trans('zohobooks::general.name')
            ]
        );

        flash($message)->success();

        return redirect()->route('zohobooks.settings.edit');
    }
}
