<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Admin extends Model
{
    use HasFactory;

    public $table = 'admins_profile';
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'account_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birth_date',
        'email_address',
        'position',
        'mobile_number'
    ];

    protected $attributes = [
        'email_address_verified' => false
    ];

    protected $hidden = [
        'password'
    ];

    public function account() {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
