<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravel\Pail\ValueObjects\Origin\Console;

class RegistrationDetails extends Mailable
{
    use Queueable, SerializesModels;

    protected $client;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Thanks {$this->client->name} - for registering with us")
            ->markdown('emails.client.register_client', [
                'client' => $this->client
            ]);
    }
}
