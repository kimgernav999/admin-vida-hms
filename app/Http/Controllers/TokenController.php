<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\EmailVerification;
use App\Models\EmailVerifyToken;
use App\Models\PassChangeToken;
use App\Models\Account;
use App\Models\Admin;

class TokenController extends Controller
{
    public function createToken($type, Request $request)
    {
        $verification_token = '';
        $message = '';

        $user = Admin::where('email_address', $request->email_address)
            ->first();

        if($user) {
            switch ($type) {
                case 'emailverify':
                    $verification_token = Hash::make(Str::random(8));

                    EmailVerifyToken::where([
                            'email_address' => $request->email_address
                        ])->delete();

                    EmailVerifyToken::create([
                        'email_address' => $request->email_address,
                        'verification_token' =>  $verification_token
                    ]);

                    try {
                        Mail::mailer('smtp')
                            ->to($request->email_address)
                            ->send(new EmailVerification($verification_token, $type));

                        $message = 'Email Verification Link has been sent to your email: \'' . $request->email_address . '\'!';
                    } catch (Throwable $th) {
                        $message = 'Email Verification Link sending failed!';
                    }
                    break;

                case 'passchange':
                    $reset_token = Hash::make(Str::random(8));

                    PassChangeToken::where([
                            'email_address' => $request->email_address
                        ])->delete();

                    PassChangeToken::create([
                        'email_address' => $request->email_address,
                        'reset_token' =>  $reset_token
                    ]);

                    try {
                        Mail::mailer('smtp')
                            ->to($request->email_address)
                            ->send(new EmailVerification($reset_token, $type));

                        $message = 'Password Change Link has been sent to your email: \'' . $request->email_address . '\'!';
                    } catch (Throwable $th) {
                        $message = 'Password Change Link sending failed!';
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        else {
            $message = 'Email is not linked to any account!';

        }

        return response(
            array(
                'message' => $message
            )
        );
    }

    public function verifyToken(Request $request)
    {
        $verification_token = urldecode($request->verification_token);
        $token_info = [];
        $title = '';
        $message = '';

        switch ($request->type) {
            case 'emailverify':
                $token_info = EmailVerifyToken::where([
                        'verification_token' => $verification_token
                    ])->first();

                $title = 'Email Verification Notice';

                if($token_info) {
                    $token_info->delete();

                    $user = Admin::where('email_address', $token_info->email_address)
                        ->first();

                    $user->email_address_verified = true;

                    $message = $user->save() ? 'Email Verification success! You may sign in now.' : 'Email Verification Failed!';
                }
                else {
                    $message = 'Email Verification token is invalid!';
                }

                return view('mails.email-notice', [
                    'message' => $message,
                    'title' => $title
                ]);
                break;

            case 'passchange':
                $token_info = PassChangeToken::where([
                        'reset_token' => $verification_token
                    ])->first();

                $title = 'Password Notice';

                if($token_info) {
                    $user = Admin::where('email_address', $token_info->email_address)
                        ->first();

                    return view('mails.change-pass-now', [
                        'changepass_for' => $user->first_name . ' ' . ($user->middle_name ? substr($user->middle_name, 0, 1) . ' ' : '') . $user->last_name,
                        'changepass_for_username' => Account::where('account_id', $user->account_id)->first()->username
                    ]);
                }
                else {
                    $message = 'Change password token is invalid!';

                    return view('mails.email-notice', [
                        'message' => $message,
                        'title' => $title
                    ]);
                }
                break;

            default:
                # code...
                break;
        }
    }

    public function updatePassword(Request $request)
    {
        $token_info = PassChangeToken::where([
                'reset_token' => $request->verification_token
            ])->first();

        if($token_info) {
            $token_info->delete();

            $account = Account::where('username', $request->username)
                ->first();

            $account->password = Hash::make($request->password);

            $message = $account->save() ? 'Password has been reset! You may sign in now.' : 'Password reset failed!';
        }
        else {
            $message = 'Password Change token is invalid!';
        }

        return response(
            array(
                'message' => $message
            )
        );
    }
}
