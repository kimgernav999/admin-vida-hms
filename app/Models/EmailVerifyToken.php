<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\User;

class EmailVerifyToken extends Model
{
    use HasFactory;

    public $table = 'email_verification_tokens';

    protected $fillable = [
        'email_address',
        'verification_token'
    ];

    public function user() {
        return $this->hasOne(User::class, 'email_address', 'email_address');
    }
}
