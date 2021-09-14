<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use App\Models\User;

class AccountsController extends Controller
{
    public function signup(Request $request)
    {
        $message = '';

        $new_account = Account::create([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        if(!$new_account) {
            $message = 'Sign up failed!';
        }

        $new_user = User::create([
            'account_id' => $new_account->account_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email_address' => $request->email_address
        ]);

        if(!$new_user) {
            $new_account->delete();
            $message = 'Sign up failed!';
        }

        $expiredate = Carbon::now('Asia/Manila')->addHours(1);

        session(['username' => $request->username]);
        session(['expires_at' => $expiredate]);

        $message = 'Sign up success!';

        return response(
            array(
                'account' => $new_account,
                'user_profile' => $new_user,
                'message' => $message
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
        $curr_user = Account::with('user')
            ->where('username', session('username'))
            ->first();

        return response(
            array(
                'username' => $curr_user ? session('username') : null,
                'expires_at' => $curr_user ? session('expires_at') : null,
                'current' => $curr_user
            )
        );
    }

    public function updateProfile(Request $request)
    {
        $curr_account = Account::where('username', session('username'))
            ->first();

        $user_profile = User::where('account_id', $curr_account->account_id)
                ->first();

        $user_profile->first_name = $request->first_name;
        $user_profile->middle_name = $request->middle_name;
        $user_profile->last_name = $request->last_name;
        $user_profile->email_address = $request->email_address;
        $user_profile->mobile_number = $request->mobile_number;

        $message = $user_profile->save() ? "Profile Updated!" : "Profile Update Failed!";

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
            $curr_account->password = $request->new_password;

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
        $is_registration = session('username');

        if(!$is_registration){
            $user = User::where([
                'email_address' => $request->email_address
            ])->first();
        }
        else {
            $curr_user = Account::with('user')
                ->where('username', session('username'))
                ->first();

            $user = User::where('email_address', $request->email_address)
                ->where('email_address', '!=', $curr_user->user->email_address ?? null)
                ->first();
        }

        return $user ? 1 : 0;
    }

    public function checkUsername(Request $request)
    {
        $is_registration = session('username');

        if(!$is_registration){
            $user = Account::where([
                'username' => $request->username
            ])->first();
        }
        else {
            $user = Account::where('username', $request->username)
                ->where('username', '!=', session('username'))
                ->first();
        }

        return $user ? 1 : 0;
    }

    public function test(Request $request)
    {
        $curr_account = Account::where('username', session('username'))
            ->first();

        $user_profile = User::where('account_id', $curr_account->account_id)
                ->first();

        return $user_profile->update($request->all());
    }
}
