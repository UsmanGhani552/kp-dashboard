<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'price',
        'tip',
        'remaining_price',
        'status',
        'description',
        'category_id',
        'package_id',
        'assigned_package_id',
        'payment_type_id',
        'brand_id',
        'sale_type',
    ];

    public static function createInvoice(array $data): self
    {
        return self::create($data);
    }

    public function updateInvoice(array $data): void
    {
        $this->update($data);
    }
    public function deleteInvoice(): void
    {
        $this->delete();
    }

    /**
     * Get the client that owns the invoice.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withDefault();
    }

    public function assignedPackage()
    {
        return $this->belongsTo(ClientAssignedPackage::class)->withDefault();
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
