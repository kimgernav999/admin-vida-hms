<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use App\Models\Admin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $new_account = Account::create([
            'username' => 'superadmin',
            'password' => Hash::make('Superadmin12345'),
            'user_role' => 'employee'
        ]);

        $new_user = Admin::create([
            'account_id' => $new_account->account_id,
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'gender' => 'Male',
            'birth_date' => '1999-01-01',
            'email_address' => 'admin@vida-emirates.cf',
            'position' => 'Administrator'
        ]);

    }
}
