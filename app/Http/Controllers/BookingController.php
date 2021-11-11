<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Account;

class BookingController extends Controller
{
    public function new(Request $request)
    {
        $user = Account::where('username', session('username'))->first();

        $book_info = $request->book_info;
        $selected_room = $request->selected_room;

        $booking = Booking::create([
            'booking_code' => BookingController::generateCode(),
            'room_id' => $selected_room['room_id'],
            'account_id' => $user->account_id,
            'no_of_adult' => $book_info['adult_no'],
            'no_of_child' => $book_info['child_no'],
            'book_start' => $book_info['start_date'],
            'book_end' => $book_info['end_date'],
            'is_paid' => false,
            'is_reservation' => $request->is_reservation,
            'total_fee' => $request->total_fee
        ]);

        return response($booking);
    }

    public function payBooking(Request $request)
    {
        $booking = Booking::where('booking_history_id', $request->booking_history_id)->first();

        if($booking){
            $booking->is_paid = true;
            $booking->paid_date = Carbon::now();

            return $booking->save() ? 1 : 0;
        }
        else {
            return 0;
        }
    }

    public function cancelBooking(Request $request)
    {
        $booking = Booking::where('booking_history_id', $request->booking_history_id)->first();

        if($booking){
            return $booking->delete() ? 1 : 0;
        }
        else {
            return 0;
        }
    }

    public function allBookings(Request $request)
    {
        $booking = Booking::with(['room', 'user'])->orderBy('created_at', 'DESC')->get();

        return $booking;
    }

    public static function generateCode()
    {
        do {
            $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $generated_code = substr(str_shuffle($permitted_chars), 0, 8);
            $reservation = Booking::where('booking_code', $generated_code);
        } while (isset($reservation->reservation_code));

        return $generated_code;
    }
}
