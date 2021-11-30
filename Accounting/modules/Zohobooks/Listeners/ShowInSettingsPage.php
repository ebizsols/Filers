<?php

namespace Modules\Zohobooks\Listeners;

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
        $event->modules->settings['zohobooks'] = [
            'name'        => trans('zohobooks::general.name'),
            'description' => trans('zohobooks::general.description'),
            'url'         => route('zohobooks.settings.edit'),
            'icon'        => 'fas fa-exchange-alt',
        ];
    }
}
