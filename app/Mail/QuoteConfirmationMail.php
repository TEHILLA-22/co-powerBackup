<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quote Request - Copower Wholesale',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-confirmation',
            with: [
                'quote' => $this->quote,
                'user' => $this->quote->user,
                'quoteUrl' => route('quote.confirmation', $this->quote),
                'trackUrl' => route('quote.track'),
                'trackResultUrl' => $this->quote->user
                    ? route('quote.track', [
                        'reference' => $this->quote->quote_number,
                        'email' => $this->quote->user->email,
                    ])
                    : route('quote.track'),
            ]
        );
    }
}