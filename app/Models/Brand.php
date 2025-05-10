<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Observers\BrandObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

class Brand extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'brands';

    protected $fillable = ['name', 'slug', 'logo'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = self::generateUniqueSlug($value);
    }

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

    protected static function boot()
    {
        parent::boot();

        // Hapus file logo saat brand dihapus permanen
        static::deleting(function ($brand) {
            if ($brand->isForceDeleting() && $brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
        });

        // Hapus logo lama saat logo diupdate
        static::updating(function ($brand) {
            $originalLogo = $brand->getOriginal('logo');
            $newLogo = $brand->logo;

            if ($originalLogo && $originalLogo !== $newLogo) {
                Storage::disk('public')->delete($originalLogo);
            }
        });
    }
}
