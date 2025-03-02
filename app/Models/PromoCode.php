<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCode extends Model
{
    use HasFactory;
    protected $table = 'promo_codes';
    //
    protected $fillable = ['code', 'discount_amount'];
}
