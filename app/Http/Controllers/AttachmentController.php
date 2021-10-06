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
        $filename = '';
        $file_exist = false;

        do {
            $filename = 'uploads_' . AttachmentController::generateRandomString();
            $file_exist = file_exists($destinationPath . $filename);
        } while ($file_exist);

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

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
