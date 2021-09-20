<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Room;

class RoomTypeController extends Controller
{
    public function create(Request $request) {
        $room_type = RoomType::create($request->all());
        $message = $room_type ? 'Room Type record saved!' : 'Room Type record save failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function update(Request $request) {
        $room_type = RoomType::where('room_type_id', $request->room_type_id)->first();
        $room_type->room_type_name = $request->room_type_name;
        $room_type->description = $request->description;
        $message = $room_type->save() ? 'Room Type record updated!' : 'Room Type record update failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function delete(Request $request) {
        $room_type = RoomType::where('room_type_id', $request->room_type_id)->first();
        $message = $room_type->delete() ? 'Room Type record deleted!' : 'Room Type record delete failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function allRoomTypes(Request $request) {
        return RoomType::all();
    }
}
