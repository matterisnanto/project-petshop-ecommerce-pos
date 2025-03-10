<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    //
    protected $fillable = ['name', 'slug', 'barcode', 'thumbnail', 'about', 'purchase_price', 'selling_price', 'is_active', 'is_popular', 'stock', 'category_id', 'brand_id', 'supplier_id'];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug. '-'. $counter++;
            $counter++;
        }
        return $slug;
    }

    public function getImageUrlAttribute()
{
    return $this->thumbnail ? url('storage/' . $this->thumbnail) : null;
}


public function scopeSearch($query, $value)
{
    return $query->where("name", "like", "%{$value}%");
}


    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }


}