<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Amenities;

class AmenitiesCategory extends Model
{
    use HasFactory;

    public $table = 'amenities_category';
    public $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'description'
    ];

    public function amenities() {
        return $this->hasMany(Amenities::class, 'category_id', 'category_id');
    }
}
