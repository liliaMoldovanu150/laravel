<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderProducts;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $orderProducts)
    {
        $this->order = $order;
        $this->orderProducts = $orderProducts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.email')
            ->subject(__('labels.order') . ': ' . $this->order->id);
    }
}
