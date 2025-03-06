<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProduct extends Model
{
    use HasFactory;
    
    protected $table = 'order_products';

    protected $fillable = ['pos_transaction_id', 'product_id', 'quantity', 'unit_price'];


    public function posTransaction()
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }
        
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}