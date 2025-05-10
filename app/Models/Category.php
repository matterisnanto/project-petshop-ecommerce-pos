<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
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

    protected static function boot()
    {
        parent::boot();

        // Hapus file logo saat brand dihapus permanen
        static::deleting(function ($category) {
            if ($category->isForceDeleting() && $category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
        });

        // Hapus icon lama saat icon diupdate
        static::updating(function ($category) {
            $originalicon = $category->getOriginal('icon');
            $newicon = $category->icon;

            if ($originalicon && $originalicon !== $newicon) {
                Storage::disk('public')->delete($originalicon);
            }
        });
    }
}
