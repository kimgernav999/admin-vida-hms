<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use App\Models\Admin;
use App\Models\User;

class AccountsController extends Controller
{
    public function create(Request $request)
    {
        $new_account = Account::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_role' => $request->user_role
        ]);

        $new_user = null;

        switch($request->user_role){
            case 'employee':
                $new_user = Admin::create([
                    'account_id' => $new_account->account_id,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'birth_date' => $request->birth_date,
                    'email_address' => $request->email_address,
                    'mobile_number' => $request->mobile_number,
                    'position' => $request->position
                ]);
                break;

            case 'guest':
                $new_user = User::create([
                    'account_id' => $new_account->account_id,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'birth_date' => $request->birth_date,
                    'email_address' => $request->email_address,
                    'mobile_number' => $request->mobile_number
                ]);
                break;
        }

        if(!$new_user) {
            $new_account->delete();
        }

        return response(
            array(
                'message' =>  ($new_account && $new_user) ? 'Employee registration success!' : 'Employee registration failed!'
            )
        );
    }

    public function delete(Request $request) {
        $del_account = Account::where('account_id', $request->account_id)
            ->first();

        $del_profile = Admin::where('account_id', $request->account_id)
            ->first();

        if($del_account && $del_profile){
            $del_account = $del_account->delete();
            $del_profile = $del_profile->delete();
        }

        return response(
            array(
                'message' => ($del_account && $del_profile) ? 'Employee Record deleted!' : 'Employee Record delete failed!'
            )
        );
    }

    public function signin(Request $request)
    {
        $message = '';

        $signin_account = Account::where([
            'username' => $request->username
        ])->first();

        if($signin_account) {
            if (Hash::check($request->password, $signin_account->password)) {
                $expiredate = $request->remember
                    ? Carbon::now('Asia/Manila')->addDays(7)
                    : Carbon::now('Asia/Manila')->addHours(1);

                session(['username' => $request->username]);
                session(['expires_at' => $expiredate]);

                $message = 'Sign In successful!';
            } else {
                $message = 'Password incorrect!';
            }
        }
        else {
            $message = 'User Account not found!';
        }

        return response(
            array(
                'username' => session('username'),
                'expires_at' => session('expires_at'),
                'message' => $message
            )
        );
    }

    public function signout(Request $request)
    {
        session(['username' => null]);
        session(['expires_at' => null]);
    }

    public function current(Request $request)
    {
        $curr_account = Account::with('user', 'admin')
            ->where('username', session('username'))
            ->first();

        $user_profile = '';

        if($curr_account) {
            switch ($curr_account->user_role) {
                case 'employee':
                    $user_profile = Admin::where('account_id', $curr_account->account_id)
                        ->first();
                    break;

                case 'guest':
                    $user_profile = User::where('account_id', $curr_account->account_id)
                        ->first();
                    break;
            }
        }

        return response(
            array(
                'username' => $curr_account ? session('username') : null,
                'expires_at' => $curr_account ? session('expires_at') : null,
                'current' => $curr_account
            )
        );
    }

    public function updateProfile(Request $request)
    {
        $curr_account = Account::where(isset($request->account_id) ? 'account_id' : 'username', isset($request->account_id) ? $request->account_id : session('username'))
            ->first();

        $email_changed = $request->email_address != $request->old_email_address;

        $user_profile = '';
        $saved = false;

        switch ($curr_account->user_role) {
            case 'employee':
                $user_profile = Admin::where('account_id', $curr_account->account_id)
                    ->first();

                if(isset($request->username))
                    $curr_account->username = $request->username;

                if(isset($request->password))
                    $curr_account->password = Hash::make($request->password);

                $user_profile->first_name = $request->first_name;
                $user_profile->middle_name = $request->middle_name;
                $user_profile->last_name = $request->last_name;
                $user_profile->gender = $request->gender;
                $user_profile->birth_date =  $request->birth_date;
                $user_profile->email_address = $request->email_address;
                $user_profile->mobile_number = $request->mobile_number;
                $user_profile->position = $request->position;

                if($email_changed)
                    $user_profile->email_address_verified = false;

                $saved = $user_profile->save() && $curr_account->save();
                break;

            case 'guest':
                $user_profile = User::where('account_id', $curr_account->account_id)
                    ->first();

                if(isset($request->username))
                    $curr_account->username = $request->username;

                if(isset($request->password))
                    $curr_account->password = Hash::make($request->password);

                $user_profile->first_name = $request->first_name;
                $user_profile->middle_name = $request->middle_name;
                $user_profile->last_name = $request->last_name;
                $user_profile->gender = $request->gender;
                $user_profile->birth_date =  $request->birth_date;
                $user_profile->email_address = $request->email_address;
                $user_profile->mobile_number = $request->mobile_number;

                if($email_changed)
                    $user_profile->email_address_verified = false;

                $saved = $user_profile->save() && $curr_account->save();
                break;

            default:
                # code...
                break;
        }

        $message = $saved ? "Profile Updated!" : "Profile Update Failed!";

        return response(
            array(
                'message' => $message
            )
        );
    }

    public function updateUsername(Request $request)
    {
        $curr_account = Account::where('username', session('username'))
            ->first();

        $message = '';

        if(Hash::check($request->password, $curr_account->password)){
            $curr_account->username = $request->username;

            if($curr_account->save()){
                $message = 'Username Updated!';

                session(['username' => $curr_account->username]);
            }
            else {
                $message = 'Saving Failed!';
            }
        }
        else {
            $message = 'Password Incorrect!';
        }

        return response(
            array(
                'message' => $message
            )
        );
    }

    public function updatePassword(Request $request)
    {
        $curr_account = Account::where('username', session('username'))
            ->first();

        $message = '';

        if(Hash::check($request->old_password, $curr_account->password)){
            $curr_account->password = Hash::make($request->new_password);

            if($curr_account->save()){
                $message = 'Password Updated!';
            }
            else {
                $message = 'Saving Failed!';
            }
        }
        else {
            $message = 'Password Incorrect!';
        }

        return response(
            array(
                'message' => $message
            )
        );
    }

    public function checkEmail(Request $request)
    {
        switch ($request->user_role) {
            case 'employee':
                if(isset($request->username)){
                    $curr_user = Account::with('admin')
                        ->where('username', $request->username)
                        ->first();

                    $user = Admin::where('email_address', $request->email_address)
                        ->where('email_address', '!=', $curr_user->admin->email_address ?? null)
                        ->first();
                }
                else {
                    $curr_user = Account::with('admin')
                        ->where('username', session('username'))
                        ->first();

                    $user = Admin::where('email_address', $request->email_address)
                        ->where('email_address', '!=', $curr_user->admin->email_address ?? null)
                        ->first();
                }
                break;

            case 'guest':
                if(isset($request->username)){
                    $curr_user = Account::with('admin')
                        ->where('username', $request->username)
                        ->first();

                    $user = User::where('email_address', $request->email_address)
                    ->where('email_address', '!=', $curr_user->user->email_address ?? null)
                    ->first();
                }
                else {
                    $curr_user = Account::with('user')
                        ->where('username', session('username'))
                        ->first();

                    $user = User::where('email_address', $request->email_address)
                        ->where('email_address', '!=', $curr_user->user->email_address ?? null)
                        ->first();
                }
                break;

        }

        return $user ? 1 : 0;
    }

    public function checkUsername(Request $request)
    {
        if(isset($request->old_username)){
            $user = Account::where('username', $request->username)
                ->where('username', '!=', $request->old_username)
                ->first();
        }
        else {
            $user = Account::where('username', $request->username)
                ->where('username', '!=', session('username'))
                ->first();
        }

        return $user ? 1 : 0;
    }

    public function allAccounts(Request $request)
    {
        $all_accounts = [];

        switch ($request->user_role) {
            case 'employee':
                $all_accounts = Account::with('admin')
                    ->where('user_role', 'employee')
                    ->where('username', '!=', 'superadmin')
                    ->get();
                break;

            case 'guest':
                $all_accounts = Account::with('user')
                    ->where('user_role', 'guest')
                    ->get();
                break;
        }

        return $all_accounts;
    }

    public function test(Request $request)
    {
        $curr_account = Account::where('username', session('username'))
            ->first();

        $user_profile = Admin::where('account_id', $curr_account->account_id)
                ->first();

        return $user_profile->update($request->all());
    }
}
