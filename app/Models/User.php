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
        'middle_name',
        'last_name',
        'gender',
        'birth_date',
        'email_address',
        'mobile_number',
    ];

    protected $attributes = [
        'email_address_verified' => false,
        'paypal_id' => null
    ];

    protected $hidden = [
        'password'
    ];

    public function account() {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
