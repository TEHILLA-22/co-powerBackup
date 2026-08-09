<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otp
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your email - Copower Wholesale',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.otp-verification',
            with: [
                'user' => $this->user,
                'otp' => $this->otp,
                'name' => $this->user->full_name,
                'expiry_minutes' => 10,
            ]
        );
    }
}