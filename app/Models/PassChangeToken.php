<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\User;

class PassChangeToken extends Model
{
    use HasFactory;

    public $table = 'password_reset_tokens';

    protected $fillable = [
        'email_address',
        'reset_token'
    ];

    public function user() {
        return $this->hasOne(User::class, 'email_address', 'email_address');
    }
}
