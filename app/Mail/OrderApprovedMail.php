<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order ' . ($this->order->order_number ?? '') . ' Has Been Approved - Copower Wholesale',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-approved',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'orderUrl' => route('order.confirmation', $this->order),
            ]
        );
    }
}