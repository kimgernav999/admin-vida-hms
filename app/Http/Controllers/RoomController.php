<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Room;

class RoomController extends Controller
{
    public function create(Request $request) {
        $room_type = RoomType::where('room_type_name', $request->room_type_name)->first();

        $room = Room::create([
            'room_type_id' => $room_type->room_type_id,
            'room_name' => $request->room_name,
            'description' => $request->description,
            'room_rate' => $request->room_rate,
            'max_adult' => $request->max_adult,
            'max_child' => $request->max_child,
            'adult_extra_rate' => $request->adult_extra_rate,
            'child_extra_rate' => $request->child_extra_rate
        ]);

        $message = $room ? 'Room record saved!' : 'Room record save failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function update(Request $request) {
        $room_type = RoomType::where('room_type_name', $request->room_type_name)->first();
        $room = Room::where('room_id', $request->room_id)->first();
        $room->room_type_id = $room_type->room_type_id;
        $room->room_name = $request->room_name;
        $room->description = $request->description;
        $room->room_rate = $request->room_rate;
        $room->max_adult = $request->max_adult;
        $room->max_child = $request->max_child;
        $room->adult_extra_rate = $request->adult_extra_rate;
        $room->child_extra_rate = $request->child_extra_rate;
        $message = $room->save() ? 'Room record updated!' : 'Room record update failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function delete(Request $request) {
        $room = Room::where('room_id', $request->room_id)->first();
        $message = $room->delete() ? 'Room record deleted!' : 'Room record delete failed!';
        return response(
            array(
                'message' => $message
            )
        );
    }

    public function allRooms(Request $request) {
        return Room::with('type')->get();
    }
}
