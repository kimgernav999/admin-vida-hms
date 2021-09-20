<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;

class RoomType extends Model
{
    use HasFactory;

    public $table = 'room_types';
    public $primaryKey = 'room_type_id';

    protected $fillable = [
        'room_type_name',
        'description'
    ];

    public function amenities() {
        return $this->hasMany(Room::class, 'room_type_id', 'room_type_id');
    }
}
