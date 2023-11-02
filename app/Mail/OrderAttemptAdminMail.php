<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderAttemptAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    protected $cartItems;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data = [], $cartItems)
    {
        $this->data = $data;
        $this->cartItems = $cartItems;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Попытка оформления заказа - ' . setting('site.title'))
                    ->view('emails.orders.attempt', ['data' => $this->data, 'cartItems' => $this->cartItems]);
    }
}

