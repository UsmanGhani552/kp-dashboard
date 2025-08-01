<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPaid extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;
    protected $isClient;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, bool $isClient = false)
    {
        $this->order = $invoice;
        $this->isClient = $isClient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject('PAYMENT RECEIVED')
                ->markdown('emails.orders.paid')
                ->with(["order" => $this->order, 'isClient' => $this->isClient]);
    }
}
