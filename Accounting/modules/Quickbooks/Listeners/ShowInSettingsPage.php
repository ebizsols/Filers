<?php

namespace Modules\Quickbooks\Listeners;

use App\Events\Module\SettingShowing as Event;

class ShowInSettingsPage
{
    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        $event->modules->settings['quickbooks'] = [
            'name'        => trans('quickbooks::general.name'),
            'description' => trans('quickbooks::general.description'),
            'url'         => route('quickbooks.settings.edit'),
            'icon'        => 'fas fa-exchange-alt',
        ];
    }
}
