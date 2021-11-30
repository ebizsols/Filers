<?php

namespace App\Observers;

use App\Events\NewChatEvent;
use App\Events\NewMessage;
use App\Models\UserChat;

class NewChatObserver
{

    public function created(UserChat $userChat)
    {
        if (!isRunningInConsoleOrSeeding() ) {
            event(new NewChatEvent($userChat));
            event(new NewMessage($userChat));
        }
    }

}
