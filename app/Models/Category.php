<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    //
    protected $fillable = ['name', 'slug', 'icon'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
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

    public function scopeWithProductsCount($query)
    {
        return $query->withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }]);
    }

    // Di model Category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = collect();
        $category = $this;

        while ($category) {
            $breadcrumbs->prepend($category);
            $category = $category->parent;
        }

        return $breadcrumbs;
    }
}
