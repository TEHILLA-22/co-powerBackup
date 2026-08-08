<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Copower Wholesale Account - Pending Approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registration-pending',
            with: [
                'user' => $this->user,
                'name' => $this->user->full_name,
                'company' => $this->user->company_name,
            ]
        );
    }
}