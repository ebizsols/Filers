<?php

namespace App\Http\Controllers;

use App\Models\DiscussionFile;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;

class DiscussionFilesController extends AccountBaseController
{

    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {

            foreach ($request->file as $fileData){
                $file = new DiscussionFile();
                $file->user_id = $this->user->id;

                if($request->has('type')) {
                    $file->discussion_id = $request->discussion_id;
                }
                else {
                    $file->discussion_reply_id = $request->discussion_id;
                }

                $filename = Files::uploadLocalOrS3($fileData, 'discussion-files/');

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();

                $file->save();
            }

        }

        $this->DiscussionFiles = DiscussionFile::where('discussion_id', $request->discussion_id)->get();

        return Reply::success(__('messages.fileUploaded'));
    }

    public function destroy(Request $request, $id)
    {
        $file = DiscussionFile::findOrFail($id);

        Files::deleteFile($file->hashname, 'discussion-files/');

        DiscussionFile::destroy($id);

        return Reply::success(__('messages.fileDeleted'));
    }

    public function download($id)
    {
        $file = DiscussionFile::whereRaw('md5(id) = ?', $id)->firstOrFail();
        return download_local_s3($file, 'discussion-files/'.$file->hashname);
    }

}
