<?php

namespace App\Models;

use App\Models\Brands;
use App\Models\Supplier;
use App\Models\Categories;
use App\Models\DetailOrder;
use Illuminate\Support\Str;
use App\Models\ProductPhoto;
use App\Models\CategoryAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'products';
    //
    protected $fillable = ['name', 'slug', 'barcode', 'thumbnail', 'description', 'weight', 'purchase_price', 'selling_price', 'is_active', 'is_popular', 'stock', 'category_id', 'brand_id', 'category_animals_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class, 'category_animals_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
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
        return $this->thumbnail ? url('storage/', $this->thumbnail) : null;
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopePriceAsc($query)
    {
        return $query->orderBy('selling_price', 'asc');
    }

    public function scopePriceDesc($query)
    {
        return $query->orderBy('selling_price', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function detail_order(): HasMany
    {
        return $this->hasMany(DetailOrder::class);
    }
}
