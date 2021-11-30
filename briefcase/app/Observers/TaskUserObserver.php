<?php

namespace App\Observers;

use App\Events\TaskEvent;
use App\Models\TaskUser;

class TaskUserObserver
{
    
    public function saved(TaskUser $taskUser)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (user() && $taskUser->user_id != user()->id) {
                event(new TaskEvent($taskUser->task, $taskUser->user, 'NewTask'));
            }
        }
    }

}
