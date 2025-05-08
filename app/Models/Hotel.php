<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $table = 'hotels';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'description', 'price_per_day', 'capacity', 'thumbnail', 'is_active'];

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }

    public function hotelPackage()
    {
        return $this->hasMany(HotelPackage::class);
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

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
