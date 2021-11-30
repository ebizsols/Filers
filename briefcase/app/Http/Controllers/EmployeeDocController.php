<?php

namespace App\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\EmployeeDocs\CreateRequest;
use App\Models\EmployeeDocs;
use Illuminate\Http\Request;

class EmployeeDocController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employeeDocs';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
    }

    public function store(CreateRequest $request)
    {
        $fileFormats = ['image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf', 'text/plain'];

        foreach ($request->file as $index => $fFormat) {
            if (!in_array($fFormat->getClientMimeType(), $fileFormats)) {
                return Reply::error(__('meesages.employeeDocsAllowedFormat'));
            }
        }

        $file = new EmployeeDocs();
        $file->user_id = $request->user_id;

        $file->name = $request->name;
        $file->filename = $request->file->getClientOriginalName();
        $file->hashname = Files::uploadLocalOrS3($request->file, 'employee-docs/' . $request->user_id);
        $file->size = $request->file->getSize();
        $file->save();

        $this->files = EmployeeDocs::where('user_id', $request->user_id)->orderBy('id', 'desc')->get();
        $view = view('employees.files.show', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function destroy(Request $request, $id)
    {
        $file = EmployeeDocs::findOrFail($id);
        $deleteDocumentPermission = user()->permission('delete_documents');
        abort_403(!($deleteDocumentPermission == 'all' || ($deleteDocumentPermission == 'added' && $file->added_by == user()->id)));

        Files::deleteFile($file->hashname, 'employee-docs/' . $file->user_id);

        EmployeeDocs::destroy($id);

        $this->files = EmployeeDocs::where('user_id', $file->user_id)->orderBy('id', 'desc')->get();

        $view = view('employees.files.show', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['view' => $view]);

    }

    public function show($id)
    {
        $file = EmployeeDocs::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_documents');

        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        $this->filepath = $file->doc_url;
        return view('employees.files.view', $this->data);

    }

    public function download($id)
    {
        $file = EmployeeDocs::whereRaw('md5(id) = ?', $id)->firstOrFail();
        $this->viewPermission = user()->permission('view_documents');

        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $file->added_by == user()->id)));

        return download_local_s3($file, 'employee-docs/' . $file->user_id . '/' . $file->hashname);

    }

}
