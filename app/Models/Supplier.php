<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = ['name', 'email', 'phone', 'address', 'status'];


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query) //
    {
        return $query->where('status', true);
    }

    protected function phone(): Attribute
    {
    return Attribute::make(
        get: fn ($value) => str_starts_with($value, '+62') ? $value : '+62' . $value,
        set: fn ($value) => ltrim($value, '+62 ') // Menghindari duplikasi +62
    );
    }

    /**
     * Mutator: Capitalize the supplier name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }
}