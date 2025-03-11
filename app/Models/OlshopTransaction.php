<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OlshopTransaction extends Model
{
    protected $table = 'olshop_transactions';
    //
    protected $fillable = ['name', 'phone', 'email', 'product_id', 'promo_code_id', 'quantity', 'sub_total_amount', 'grand_total_amount', 'discount_amount', 'province', 'city_regency', 'district', 'vilage_subdistrict', 'post_code', 'address', 'city', 'is_paid', 'trx_id', 'proof'];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function promocode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn($value) => str_starts_with($value, '+62') ? $value : '+62' . $value,
            set: fn($value) => ltrim($value, '+62 ') // Menghindari duplikasi +62
        );
    }


    // public function subtotalamount() {
    //     return $this->belongsTo(SubTotalAmount::class);
    // }


    // public function grandtotalamount() {
    //     return $this->belongsTo(GrandTotalAmount::class);
    // }


    // public function discountamount() {
    //     return $this->belongsTo(DiscountAmount::class);
    // }

}
