<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionReturn extends Model
{
    protected $table = 'transaction_returns';
    //
    protected $fillable = ['return_date', 'return_number', 'type', 'pos_transaction_id', 'olshop_transaction_id', 'refund_amount', 'status', 'return_approved_date', 'notes'];

    public function postransaction()
    {
        return $this->belongsTo(PosTransaction::class);
    }

    public function olshoptransaction()
    {
        return $this->belongsTo(OlshopTransaction::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'purchase_return_id');
    }
}
