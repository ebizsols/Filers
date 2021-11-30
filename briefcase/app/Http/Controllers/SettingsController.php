<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Helper\Reply;
use App\Http\Requests\Settings\UpdateOrganisationSettings;
use App\Models\Setting;
use App\Traits\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class SettingsController extends AccountBaseController
{
    use CurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.accountSettings';
        $this->activeSettingMenu = 'company_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!(user()->permission('manage_company_setting') == 'all'));
            return $next($request);
        });
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->currencies = Currency::all();
        $this->dateObject = Carbon::now();
        $this->cachedFile = File::exists(base_path('bootstrap/cache/config.php'));
        return view('company-settings.index', $this->data);
    }

    // phpcs:ignore
    public function update(UpdateOrganisationSettings $request, $id)
    {
        config(['filesystems.default' => 'local']);
        $setting = Setting::first();
        $setting->company_name = $request->company_name;
        $setting->company_email = $request->company_email;
        $setting->company_phone = $request->company_phone;
        $setting->website = $request->website;
        $setting->address = $request->address;
        $setting->save();

        session()->forget('global_setting');

        return Reply::redirect(route('company-settings.index'), __('messages.updateSuccess'));
    }

}
