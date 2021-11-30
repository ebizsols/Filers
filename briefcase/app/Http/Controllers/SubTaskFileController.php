<?php

namespace App\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\SubTask;
use App\Models\SubTaskFile;
use App\Models\Task;
use Illuminate\Http\Request;

class SubTaskFileController extends AccountBaseController
{

    /**
     * ManageLeadFilesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.subTaskFiles';
    }

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('add_task_files');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $subTask = SubTask::with(['files'])->findOrFail($request->sub_task_id);

        if ($request->hasFile('file')) {

            foreach ($request->file as $fileData){
                $file = new SubTaskFile();
                $file->user_id = $this->user->id;
                $file->sub_task_id = $request->sub_task_id;

                $filename = Files::uploadLocalOrS3($fileData, 'sub-task-files/' . $request->sub_task_id);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->save();

                $this->logTaskActivity($subTask->id, $this->user->id, 'fileActivity');
            }
        }

        $this->task = Task::with(['subtasks', 'subtasks.files'])->findOrFail($this->subtask->task_id);
        $view = view('tasks.sub_tasks.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function show($id)
    {
        $file = SubTaskFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_task_files');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        $this->filepath = $file->file_url;
        return view('tasks.sub_tasks.files.view', $this->data);

    }

    public function destroy($id)
    {
        $file = SubTaskFile::findOrFail($id);
        $this->deletePermission = user()->permission('delete_task_files');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $file->added_by == user()->id)));

        Files::deleteFile($file->hashname, 'sub-task-files/' . $file->sub_task_id);

        SubTaskFile::destroy($id);

        $this->files = SubTaskFile::where('sub_task_id', $file->sub_task_id)->orderBy('id', 'desc')->get();
        $view = view('tasks.sub_tasks.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['view' => $view]);
    }

    public function download($id)
    {
        $file = SubTaskFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_task_files');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));
        return download_local_s3($file, 'sub-task-files/' . $file->sub_task_id . '/' . $file->hashname);
    }

}
