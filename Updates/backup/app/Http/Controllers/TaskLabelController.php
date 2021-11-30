<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\Admin\TaskLabel\StoreRequest;
use App\Models\TaskLabelList;

class TaskLabelController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.taskLabel';
    }

    public function create()
    {
        $this->taskLabels = TaskLabelList::all();
        return view('tasks.create_label', $this->data);
    }

    public function store(StoreRequest $request)
    {
        abort_403(user()->permission('task_labels') !== 'all');
        $taskLabel = new TaskLabelList();
        $this->storeUpdate($request, $taskLabel);

        $allTaskLabels = TaskLabelList::all();

        $labels = '';

        foreach ($allTaskLabels as $key => $value) {
            $labels .= '<option value="' . $value->id . '" data-content="<span class=\'badge badge-secondary\' style=\'background-color: ' . $value->label_color . '\'>' . $value->label_name . '</span>">' . $value->label_name . '</option>';
        }

        return Reply::successWithData(__('messages.taskLabel.addedSuccess'), ['data' => $labels]);
    }

    private function storeUpdate($request, $taskLabel)
    {
        $taskLabel->label_name = $request->label_name;
        $taskLabel->color = $request->color;
        $taskLabel->description = $request->description;
        $taskLabel->save();

        return $taskLabel;
    }

}
