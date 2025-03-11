<?php

namespace App\Models;

use App\Observers\OrderProductObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([OrderProductObserver::class])]
class OrderProduct extends Model
{
    use HasFactory;
    
    protected $table = 'order_products';

    protected $fillable = ['pos_transaction_id', 'product_id', 'quantity', 'unit_price'];


    public function posTransaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id'); 
    }
    
        
    public function product():BelongsTo 
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    
}