<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderItems;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->orderItems = $order->items;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('mail.new_order_subject', [
                        'number' => $this->order->order_number,
                    ]))
                    ->view('emails.new-order')
                    ->with([
                        'order' => $this->order,
                        'orderItems' => $this->orderItems,
                    ]);
    }
}
