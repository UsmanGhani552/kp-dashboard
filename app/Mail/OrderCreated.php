<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravel\Pail\ValueObjects\Origin\Console;

class OrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $invoice;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $brand = $this->invoice->brand;
        $brandLogo = $brand->logo_url ?? asset('images/default.png');
        // Log::info("Logo Url: " . $brandLogo);
        $brandName = $brand->name ?? config('app.name');

        return $this
            ->subject("NEW ORDER #{$this->invoice->id} - {$brandName}")
            ->from($brandName)
            ->markdown('emails.orders.create', [
                'invoice' => $this->invoice,
                'brandLogo' => $brandLogo,
                'brandName' => $brandName
            ]);
    }
}
