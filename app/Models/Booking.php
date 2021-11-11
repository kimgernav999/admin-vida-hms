<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Room;

class Booking extends Model
{
    use HasFactory;

    public $table = 'booking_history';
    protected $primaryKey = 'booking_history_id';

    protected $fillable = [
        'booking_code',
        'room_id',
        'account_id',
        'no_of_adult',
        'no_of_child',
        'book_start',
        'book_end',
        'is_paid',
        'paid_date',
        'is_reservation'
    ];

    public function user() {
        return $this->hasOne(User::class, 'account_id', 'account_id');
    }

    public function room() {
        return $this->hasOne(Room::class, 'room_id', 'room_id');
    }
}
