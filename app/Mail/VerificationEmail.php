<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationLink;
    public $userName;

    public function __construct($verificationLink, $userName)
    {
        $this->verificationLink = $verificationLink;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verification')
                    ->with([
                        'verificationLink' => $this->verificationLink,
                        'userName' => $this->userName,
                    ]);
    }
} 