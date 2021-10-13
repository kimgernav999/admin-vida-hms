<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AmenitiesCategory;

class Amenities extends Model
{
    use HasFactory;

    public $table = 'amenities';
    public $primaryKey = 'amenities_id';

    protected $fillable = [
        'amenities_name',
        'category_id',
        'description',
        'features',
        'image_ids'
    ];

    public function category() {
        return $this->hasOne(AmenitiesCategory::class, 'category_id', 'category_id');
    }
}
