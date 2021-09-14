<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class User extends Model
{
    use HasFactory;

    public $table = 'users_profile';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'email_address'
    ];

    protected $attributes = [
        'email_address_verified' => false,
        'middle_name' => null,
        'mobile_number' => null,
        'paypal_id' => null
    ];

    protected $hidden = [
        'password'
    ];

    public function account() {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
