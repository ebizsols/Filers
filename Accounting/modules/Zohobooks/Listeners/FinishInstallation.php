<?php

namespace Modules\Zohobooks\Listeners;

use App\Events\Module\Installed as Event;
use App\Traits\Permissions;

class FinishInstallation
{
    use Permissions;

    public $alias = 'zohobooks';

    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        if ($event->alias != $this->alias) {
            return;
        }

        $this->updatePermissions();
    }

    protected function updatePermissions()
    {
        // c=create, r=read, u=update, d=delete
        $this->attachPermissionsToAdminRoles([
            $this->alias . '-settings' => 'r,u,d',
        ]);
    }
}
