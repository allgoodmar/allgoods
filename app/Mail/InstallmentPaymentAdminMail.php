<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contact;
use App\InstallmentPayment;
use App\Product;

class InstallmentPaymentAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $contact;
    protected $installmentPayment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(InstallmentPayment $installmentPayment, Contact $contact)
    {
        $this->contact = $contact;
        $this->installmentPayment = $installmentPayment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.installment_payments.created.admin', ['contact' => $this->contact, 'installmentPayment' => $this->installmentPayment]);
    }
}

