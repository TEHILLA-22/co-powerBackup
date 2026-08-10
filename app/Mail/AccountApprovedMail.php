<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Copower Wholesale Account Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-approved',
            with: [
                'user' => $this->user,
                'name' => $this->user->full_name,
                'loginUrl' => route('login'),
                'dashboardUrl' => route('customer.products'),
            ]
        );
    }
}