<?php

namespace App\Observers;

use App\Events\TaskEvent;
use App\Events\TaskUpdated as EventsTaskUpdated;
use App\Http\Controllers\AccountBaseController;
use App\Models\Notification;
use App\Models\ProjectTimeLog;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Traits\ProjectProgress;
use App\Models\UniversalSearch;
use App\Models\User;
use Carbon\Carbon;

class TaskObserver
{

    use ProjectProgress;

    public function saving(Task $task)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $task->last_updated_by = user()->id;
        }
    }

    public function creating(Task $task)
    {
        $task->hash = \Illuminate\Support\Str::random(32);

        if (!isRunningInConsoleOrSeeding()) {
            if (user()) {
                $task->created_by = user()->id;
                $task->added_by = user()->id;
            }

            if (request()->has('board_column_id')) {
                $task->board_column_id = request()->board_column_id;
            }
            else if (isset(global_setting()->default_task_status)) {
                $task->board_column_id = global_setting()->default_task_status;
            }
            else {
                $taskBoard = TaskboardColumn::where('slug', 'incomplete')->first();
                $task->board_column_id = $taskBoard->id;
            }
        }
    }

    public function created(Task $task)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->has('project_id') && request()->project_id != 'all' && request()->project_id != '') {
                if ($task->project->client_id != null && $task->project->allow_client_notification == 'enable' && $task->project->client->status != 'deactive') {
                    event(new TaskEvent($task, $task->project->client, 'NewClientTask'));
                }
            }

            $log = new AccountBaseController();

            if (\user()) {
                $log->logTaskActivity($task->id, user()->id, 'createActivity', $task->board_column_id);
            }

            if ($task->project_id) {
                // Calculate project progress if enabled
                $log->logProjectActivity($task->project_id, __('messages.newTaskAddedToTheProject'));
                $this->calculateProjectProgress($task->project_id);
            }

            // Log search
            $log->logSearchEntry($task->id, $task->heading, 'tasks.edit', 'task');

            // Sync task users
            if (!empty(request()->user_id) && request()->template_id == '') {
                $task->users()->sync(request()->user_id);
            }

        }
    }

    // phpcs:ignore
    public function updated(Task $task)
    {
        $movingTaskId = request()->has('movingTaskId'); // If task moved in taskboard

        if (!isRunningInConsoleOrSeeding()) {

            if ($task->isDirty('board_column_id')) {

                if ($task->boardColumn->slug == 'completed') {
                    // send task complete notification
                    $admins = User::allAdmins();
                    event(new TaskEvent($task, $admins, 'TaskCompleted'));

                    $taskUser = $task->users->whereNotIn('id', $admins->pluck('id'));
                    event(new TaskEvent($task, $taskUser, 'TaskUpdated'));

                    $timeLogs = ProjectTimeLog::with('user')->whereNull('end_time')
                        ->where('task_id', $task->id)
                        ->get();

                    if ($timeLogs) {
                        foreach ($timeLogs as $timeLog) {

                            $timeLog->end_time = Carbon::now();
                            $timeLog->edited_by_user = user()->id;
                            $timeLog->save();

                            /** @phpstan-ignore-next-line */
                            $timeLog->total_hours = ($timeLog->end_time->diff($timeLog->start_time)->format('%d') * 24) + ($timeLog->end_time->diff($timeLog->start_time)->format('%H'));

                            if ($timeLog->total_hours == 0) {
                                $timeLog->total_hours = round(($timeLog->end_time->diff($timeLog->start_time)->format('%i') / 60), 2);
                            }

                            /** @phpstan-ignore-next-line */
                            $timeLog->total_minutes = ($timeLog->total_hours * 60) + ($timeLog->end_time->diff($timeLog->start_time)->format('%i'));

                            $timeLog->save();
                        }
                    }

                    if ((request()->project_id && request()->project_id != 'all') || (!is_null($task->project_id))) {
                        $project = $task->project;

                        if ($project->client_id != null && $project->allow_client_notification == 'enable' && $project->client->status != 'deactive') {
                            event(new TaskEvent($task, $project->client, 'TaskCompletedClient'));
                        }
                    }
                }
            }

            if (request('user_id')) {
                if (($movingTaskId != '' && $task->id == $movingTaskId) || $movingTaskId == '') {
                    // Send notification to user
                    event(new TaskEvent($task, $task->users, 'TaskUpdated'));

                    if ((request()->project_id != 'all') && !is_null($task->project)) {
                        if ($task->project->client_id != null && $task->project->allow_client_notification == 'enable' && $task->project->client->status != 'deactive') {
                            event(new TaskEvent($task, $task->project->client, 'TaskUpdatedClient'));
                        }
                    }
                }
            }
        }

        event(new EventsTaskUpdated($task));

        if (\user()) {
            if (($movingTaskId != '' && $task->id == $movingTaskId) || $movingTaskId == '') {
                $log = new AccountBaseController();
                $log->logTaskActivity($task->id, user()->id, 'statusActivity', $task->board_column_id);
            }
        }

        if ($task->project_id) {
            if (($movingTaskId != '' && $task->id == $movingTaskId) || $movingTaskId == '') {
                // Calculate project progress if enabled
                $this->calculateProjectProgress($task->project_id);
            }
        }
    }

    public function deleting(Task $task)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $task->id)->where('module_type', 'task')->get();

        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }

        $notifiData = ['App\Notifications\NewTask', 'App\Notifications\TaskUpdated', 'App\Notifications\TaskComment',
        'App\Notifications\TaskCommentClient', 'App\Notifications\TaskCompleted', 'App\Notifications\NewClientTask','App\Notifications\TaskCompletedClient','App\Notifications\TaskNote','App\Notifications\TaskNoteClient','App\Notifications\TaskReminder','App\Notifications\TaskUpdatedClient','App\Notifications\SubTaskCreated','App\Notifications\SubTaskCompleted'];

        Notification::whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where(function ($q) use ($task) {
                $q->where('data', 'like', '{"id":'.$task->id.',%');
                $q->orWhere('data', 'like', '%,"task_id":'.$task->id.',%');
            })->delete();

    }

    /**
     * @param Task $task
     */
    public function deleted(Task $task)
    {
        if (!is_null($task->project_id)) {
            // Calculate project progress if enabled
            $this->calculateProjectProgress($task->project_id);
        }
    }

}
