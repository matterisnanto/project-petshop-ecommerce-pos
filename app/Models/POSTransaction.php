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
  

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'pos_transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function mount()
    {
        // Ambil data dengan relasi paymentMethod
        $this->transactions = PosTransaction::with('paymentMethod')->get();
    }

    public function render()
    {
        return view('livewire.pos-transaction', [
            'transactions' => $this->transactions
        ]);
    }

}
