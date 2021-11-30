<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HideCoreJobMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hide-cron-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hide crone job message.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $company = Setting::first();

        $company->last_cron_run = Carbon::now();
        $company->save();

        $setting = Setting::first();
        $setting->hide_cron_message = 1;
        $setting->save();
    }
    
}
