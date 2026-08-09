<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $rejectionReason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Order ' . ($this->order->order_number ?? '') . ' - Copower Wholesale',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-rejected',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'reason' => $this->rejectionReason,
                'productsUrl' => route('customer.products'),
            ]
        );
    }
}