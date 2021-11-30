<?php

namespace App\Http\Requests\SubTask;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubTask extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = global_setting();
        $task = Task::find(request()->task_id);
        $startDate = $task->start_date->format($setting->date_format);

        $rules = [
            'title' => 'required',
        ];

        $dateRule = 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:' . $startDate;


        if ($task->due_date) {
            $dueDate = $task->due_date->format($setting->date_format);
            $dateRule .= '|before_or_equal:' . $dueDate;
        }

        $rules['due_date'] = $dateRule;

        return $rules;

    }

}
