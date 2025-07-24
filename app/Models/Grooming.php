<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grooming extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'groomings';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'photo', 'description', 'stock', 'purchase_price', 'selling_price', 'is_active'];

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }

    public function groomingPackage()
    {
        return $this->hasMany(GroomingPackage::class);
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

    public function detail_order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
