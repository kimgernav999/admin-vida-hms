<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $type)
    {
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = '';

        switch ($this->type) {
            case 'emailverify':
                $this->subject('Email Verification - Vida Emirate Hills Hotel');
                $view = $this->view('mails.email-verify');
                break;

            case 'passchange':
                $this->subject('Password Change - Vida Emirate Hills Hotel');
                $view = $this->view('mails.pass-change');
                break;

            default:
                return 0;
                break;
        }

        return $view->with('token', $this->token);
    }
}

