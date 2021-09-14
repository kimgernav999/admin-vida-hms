<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Account extends Model
{
    use HasFactory;

    public $table = 'accounts';
    protected $primaryKey = 'account_id';

    protected $fillable = [
        'username',
        'password'
    ];

    protected $attributes = [
        'user_role' => 'guest'
    ];

    protected $hidden = [
        'password'
    ];

    public function user() {
        return $this->hasOne(User::class, 'account_id', 'account_id');
    }
}
