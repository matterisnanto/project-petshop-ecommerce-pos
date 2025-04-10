<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCode extends Model
{
    use HasFactory;
    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'discount_amount',
        'start_date',
        'end_date',
        'is_active'
    ];

    // Scope untuk promo code yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    // Cek apakah promo code valid
    public function isValid()
    {
        return $this->is_active &&
            (!$this->start_date || $this->start_date <= now()) &&
            (!$this->end_date || $this->end_date >= now());
    }
}
