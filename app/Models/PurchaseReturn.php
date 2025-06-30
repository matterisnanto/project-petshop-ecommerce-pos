<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_return';
    protected $fillable = ['return_date', 'return_number', 'supplier_id', 'purchases_id', 'refund_amount', 'status', 'return_approved_date', 'notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }


    public function purchases()
    {
        return $this->belongsTo(Purchases::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'purchase_return_id');
    }

    public function getItemsCountAttribute()
    {
        return $this->returnItems()->count();
    }
}
