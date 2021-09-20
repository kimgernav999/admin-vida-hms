<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RoomType;

class Room extends Model
{
    use HasFactory;

    public $table = 'rooms';
    public $primaryKey = 'room_id';

    protected $fillable = [
        'room_type_id',
        'room_name',
        'description',
        'room_rate',
        'max_adult',
        'max_child',
        'adult_extra_rate',
        'child_extra_rate'
    ];

    public function type() {
        return $this->hasOne(RoomType::class, 'room_type_id', 'room_type_id');
    }
}
