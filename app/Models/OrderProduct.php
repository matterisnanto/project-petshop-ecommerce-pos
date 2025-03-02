<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProduct extends Model
{
    use HasFactory;
    
    protected $table = 'order_products';

    protected $fillable = ['payment_method_id', 'product_id', 'quantity', 'unit_price'];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}