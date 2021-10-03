<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attachment;

class AttachmentController extends Controller
{
    public function create(Request $request) {
        $destinationPath = 'storage/uploads/';

        $file = $request->image;
        $fileext = $file->getClientOriginalExtension();
        $filename = explode('/', tempnam($destinationPath, 'attachment_'))[9];
        unlink($destinationPath . $filename);
        $file->move($destinationPath, $filename . '.' . $fileext);

        $attachment = Attachment::create([
            'file_name' => $filename . '.' . $fileext
        ]);

        return $attachment ? $attachment->attachment_id : -1;
    }

    public function delete(Request $request) {
        $destinationPath = 'storage/uploads/';

        $attachment = Attachment::where('attachment_id', $request->attachment_id)->first();
        if($attachment) unlink($destinationPath . $attachment->file_name);

        return $attachment ? $attachment->delete() : 0;
    }

    public function viewDetails(Request $request) {
        $attachment = Attachment::where('attachment_id', $request->attachment_id)->first();

        return $attachment ? $attachment : -1;
    }
}
