<?php

namespace App\Models;

use App\Observers\PurchasesObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([PurchasesObserver::class])]
class Purchases extends Model
{
    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'supplier_id',
        'total_amount',
        'notes',
        'status'
    ];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Item Pembelian
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
