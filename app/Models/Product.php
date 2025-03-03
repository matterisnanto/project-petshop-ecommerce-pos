<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Table name for this model
    protected $table = 'products';
    //
    protected $fillable = ['name', 'slug', 'barcode', 'thumbnail', 'about', 'purchase_price', 'selling_price', 'is_active', 'is_popular', 'stock', 'category_id', 'brand_id', 'supplier_id'];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
            $slug = $originalSlug . '-' . $counter++;
            $counter++;
        }
        return $slug;
    }

    public function getImageUrlAttribute()
{
    return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
}

    public function scopeSearch($query, $value)
    {
       $query->where("name", "like", "%{$value}%"); 
    }
}