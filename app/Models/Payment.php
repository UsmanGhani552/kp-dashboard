<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'firstName',
        'lastName',
        'country',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'payment_gateway',
        'price',
        'discount',
        'is_paid',
        'transaction_id',
        'transaction_details',
    ];
}
