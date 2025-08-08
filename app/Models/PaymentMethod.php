<?php

namespace App\Models;

use App\Models\PosTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = ['name', 'account_number', 'image', 'olshop_transaction', 'pos_transaction', 'is_cash'];

    protected $appends = ['image_url'];

    public function posTransaction(): HasMany
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->thumbnail ? url('storage/', $this->thumbnail) : null;
    }
}
