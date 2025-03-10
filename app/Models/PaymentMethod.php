<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = ['name', 'image', 'is_cash'];

    public function postransaction(): HasMany
    {
        return $this->hasMany(PosTransaction::class);
    }
   
}