<?php

namespace App\Models;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransaction extends Model
{
    protected $table = 'pos_transactions';
    //
    protected $fillable = [
        'name', 
        'email', 
        'gender', 
        'total_price', 
        'note', 
        'payment_method_id', 
        'paid_amount', 
        'change_amount'
    ];
    

    public function paymentmethod()
     {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}