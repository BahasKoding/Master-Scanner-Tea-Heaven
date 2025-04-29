<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;

    public function __construct(User $user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->view('emails.password_reset_otp')
                    ->subject('Password Reset OTP');
    }
}