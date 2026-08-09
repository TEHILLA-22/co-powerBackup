<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewQuoteNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Quote Request ' . ($this->quote->quote_number ?? '') . ' - Copower Wholesale',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-quote',
            with: [
                'quote' => $this->quote,
                'user' => $this->quote->user,
                'quoteUrl' => route('admin.quotes.show', $this->quote),
            ]
        );
    }
}