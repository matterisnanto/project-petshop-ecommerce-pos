<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OlshopTransaction extends Model
{
    protected $table = 'olshop_transactions';
    //
    protected $fillable = ['name', 'phone', 'email', 'promo_code_id', 'sub_total_amount', 'grand_total_amount', 'discount_amount', 'province', 'city_regency', 'post_code', 'complete_address', 'is_paid', 'trx_id', 'courier', 'shipping_service', 'weight_total', 'shipping_cost', 'estimated_delivery', 'payment_method_id', 'proof'];


    public function orders(): HasMany
    {
        return $this->hasMany(order::class, 'olshop_transaction_id');
    }
    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function promocode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function paymentmethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }


    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn($value) => str_starts_with($value, '+62') ? $value : '+62' . $value,
            set: fn($value) => ltrim($value, '+62 ') // Menghindari duplikasi +62
        );
    }

    protected $casts = [
        'shipping_service' => 'array', // jika kolom ini yang menyimpan JSON
    ];
}
