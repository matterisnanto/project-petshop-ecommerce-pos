<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    use HasFactory;

    protected $table = 'order';

    protected $fillable = ['pos_transaction_id', 'olshop_transaction_id', 'product_id', 'quantity', 'unit_price'];


    public function posTransaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }
    public function olshopTransaction(): BelongsTo
    {
        return $this->belongsTo(OlshopTransaction::class, 'olshop_transaction_id');
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
