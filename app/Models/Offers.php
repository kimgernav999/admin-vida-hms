<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{
    use HasFactory;

    public $table = 'offers';
    public $primaryKey = 'offer_id';

    protected $fillable = [
        'offer_name',
        'description',
        'rates',
        'image_ids'
    ];

    protected $attributes = [
        'room_types' => '[]'
    ];
}
