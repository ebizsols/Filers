<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Currency;

class OrganisationSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $currency = Currency::where('currency_code', 'USD')->first();

        $setting = new Setting();
        $setting->company_name = 'Worksuite';
        $setting->company_email = 'company@email.com';
        $setting->company_phone = '1234567891';
        $setting->address = 'Company address';
        $setting->website = 'https://worksuite.biz';
        $setting->currency_id = $currency->id;
        $setting->timezone = 'Asia/Kolkata';
        $setting->weather_key = '';
        $setting->date_picker_format = 'dd-mm-yyyy';
        $setting->moment_format = 'DD-MM-YYYY';
        $setting->rounded_theme = 1;
        $setting->save();


    }

}
