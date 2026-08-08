<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Copower Wholesale: New Quote Request # ' . $this->order->order_number,
            from: env('MAIL_FROM_ADDRESS', 'noreply@copower.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
                'user' => $this->user,
                'orderUrl' => route('order.confirmation', $this->order),
                'trackUrl' => route('order.confirmation', $this->order),
            ]
        );
    }
}