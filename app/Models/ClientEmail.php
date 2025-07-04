<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    protected $fillable = [
        'client_id',
        'email',
    ];

    /**
     * Create or update a client email.
     */
    public static function updateClientEmails(array $data, $client_id): void
    {
        foreach ($data['emails'] as $email) {
            self::create(
                [
                    'client_id' => $client_id,
                    'email' => $email,
                ]
            );
        }
    }

    /**
     * Get the client that owns the email.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
