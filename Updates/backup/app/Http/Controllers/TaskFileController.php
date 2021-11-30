<?php

namespace App\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;

class TaskFileController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = 'app.menu.taskFiles';
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('add_task_files');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        if ($request->hasFile('file')) {
            $task = Task::findOrFail($request->task_id);

            foreach ($request->file as $fileData) {
                $file = new TaskFile();
                $file->user_id = $this->user->id;
                $file->task_id = $request->task_id;

                $filename = Files::uploadLocalOrS3($fileData, 'task-files/' . $request->task_id);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;

                $file->size = $fileData->getSize();
                $file->save();

                $this->logTaskActivity($task->id, $this->user->id, 'fileActivity', $task->board_column_id);
            }


            $this->files = TaskFile::where('task_id', $request->task_id)->orderBy('id', 'desc');
            $viewTaskFilePermission = user()->permission('view_task_files');

            if ($viewTaskFilePermission == 'added') {
                $this->files = $this->files->where('added_by', user()->id);
            }

            $this->files = $this->files->get();
            $view = view('tasks.files.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }

    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     * @throws \Throwable
     */
    public function destroy(Request $request, $id)
    {
        $file = TaskFile::findOrFail($id);
        $this->deletePermission = user()->permission('delete_task_files');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $file->added_by == user()->id)));

        Files::deleteFile($file->hashname, 'task-files/' . $file->task_id);

        TaskFile::destroy($id);

        $this->files = TaskFile::where('task_id', $file->task_id)->orderBy('id', 'desc')->get();
        $view = view('tasks.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['view' => $view]);

    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show($id)
    {
        $file = TaskFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_task_files');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        $this->filepath = $file->file_url;
        return view('tasks.files.view', $this->data);

    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $file = TaskFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_task_files');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        return download_local_s3($file, 'task-files/' . $file->task_id . '/' . $file->hashname);

    }

}
