<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'name',
        'country',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'payment_gateway',
        'price',
        'tip',
        'discount',
        'status',
        'transaction_id',
        'transaction_details',
    ];

    public static function storePaymentData($data,$paymentGateway)
    {
        $payment = new self();
        $payment->invoice_id = $data['invoice_id'] ?? null;
        $payment->name = $data['name'] ?? null;
        $payment->email = $data['email'] ?? null;
        $payment->country = $data['country'] ?? null; // Square does not provide country
        $payment->address = $data['address'] ?? null; // Square does not provide address
        $payment->state = $data['state'] ?? null; // Square does not provide state
        $payment->city = $data['city'] ?? null; // Square does not provide city
        $payment->zip = $data['zip'] ?? null; // Square does provide zip
        $payment->phone = $data['phone'] ?? null; // Square does not provide phone
        $payment->payment_gateway = $paymentGateway;
        $payment->price = $data['price'] ?? 0;
        $payment->tip = $data['tip'] ?? 0;
        $payment->status = 1;
        $payment->transaction_id = $data['transaction_id'] ?? null;
        $payment->transaction_details = json_encode($data['transaction_details'] ?? []);
        $payment->save();

        return $payment;
    }
}
