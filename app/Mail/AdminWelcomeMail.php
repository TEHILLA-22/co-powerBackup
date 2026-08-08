<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Admin $admin
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Copower Wholesale Admin Panel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.welcome',
            with: [
                'admin' => $this->admin,
                'name' => $this->admin->full_name,
                'loginUrl' => route('admin.login'),
            ]
        );
    }
}