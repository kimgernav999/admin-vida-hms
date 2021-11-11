<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
    use HasFactory;

    public $table = 'booking_history';
    protected $primaryKey = 'booking_history_id';

    protected $fillable = [
        'room_id',
        'account_id',
        'no_of_adult',
        'no_of_child',
        'book_start',
        'book_end'
    ];

    public function account() {
        return $this->hasOne('users_profile', 'account_id', 'account_id');
    }

    public function room() {
        return $this->hasOne('rooms', 'room_id', 'room_id');
    }
}
