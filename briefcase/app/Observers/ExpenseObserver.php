<?php

namespace App\Observers;

use App\Events\NewExpenseEvent;
use App\Models\Expense;
use App\Models\Notification;

class ExpenseObserver
{

    public function saving(Expense $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $expense->last_updated_by = user()->id;
        }
    }

    public function creating(Expense $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $expense->added_by = user()->id;
        }
    }

    public function created(Expense $expense)
    {
        $userType = '';

        if (!isRunningInConsoleOrSeeding() ) {
            // Default status is approved means it is posted by admin
            if ($expense->status == 'approved') {
                $userType = 'admin';
            }

            // Default status is pending that mean it is posted by member
            if ($expense->status == 'pending') {
                $userType = 'member';
            }

            if ($expense->user_id != user()->id) {
                event(new NewExpenseEvent($expense, $userType));
            }
        }
    }

    public function updated(Expense $expense)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($expense->isDirty('status') && $expense->user_id != user()->id) {
                event(new NewExpenseEvent($expense, 'status'));
            }

        }
    }

    public function deleting(Expense $expense)
    {
        $notifiData = ['App\Notifications\NewExpenseAdmin', 'App\Notifications\NewExpenseMember','App\Notifications\NewExpenseStatus'];

        Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$expense->id.',%')
            ->delete();
    }

}
