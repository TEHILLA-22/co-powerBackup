<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Copower Wholesale Account Application',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-rejected',
            with: [
                'user' => $this->user,
                'name' => $this->user->full_name,
                'reason' => $this->reason,
                'contactUrl' => route('contact'),
            ]
        );
    }
}