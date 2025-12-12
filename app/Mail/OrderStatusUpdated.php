<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable implements ShouldQueue

{
    use Queueable, SerializesModels;

    public Order $order;
    public OrderLog $log;  // Changer 'Log' en 'log' pour la cohérence (et corriger la casse)

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, OrderLog $orderLog)
    {
        $this->order = $order;
        $this->log = $orderLog;  // Utiliser $orderLog passé en paramètre
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Order Status Updated --'.config('app.name');
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_status_updated',
            with: [
                'order' => $this->order,
                'log' => $this->log,  // Changer 'Log' en 'log'
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}