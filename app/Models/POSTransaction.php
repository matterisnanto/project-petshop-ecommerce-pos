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
        'phone',
        'email',
        'gender',
        'total_price',
        'note',
        'payment_method_id',
        'paid_amount',
        'change_amount',
        'trx_id',
    ];


    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function render()
    {
        return view('livewire.pos-transaction', [
            'transactions' => $this->transactions
        ]);
    }

    public function productOrders()
    {
        return $this->hasMany(Order::class)->whereNotNull('product_id');
    }

    public function animalOrders()
    {
        return $this->hasMany(Order::class)->whereNotNull('animals_id');
    }
}
