<?php

namespace App\Models;

use App\Models\Supplier;
use App\Models\DetailOrder;
use App\Observers\PurchasesObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([PurchasesObserver::class])]
class Purchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'supplier_id',
        'total_amount',
        'notes',
        'status',
        'proof',
    ];

    // Relasi ke Supplier
    public function orders()
    {
        return $this->hasMany(DetailOrder::class, 'purchases_id'); // Tambahkan foreign key
    }

    // Tambahkan relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
