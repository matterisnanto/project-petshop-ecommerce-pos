<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OlshopTransaction extends Model
{
    protected $table = 'olshop_transactions';
    //
    protected $fillable = ['name', 'phone', 'email', 'product_id', 'promo_code_id', 'quantity', 'sub_total_amount', 'grand_total_amount', 'discount_amount', 'address', 'post_code', 'city', 'is_paid', 'booking_trx', 'proof'];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function promocode()
    {
        return $this->belongsTo(PromoCode::class);
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
