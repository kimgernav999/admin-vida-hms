<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    public $table = 'attachments';
    public $primaryKey = 'attachment_id';

    protected $fillable = [
        'file_name'
    ];
}
