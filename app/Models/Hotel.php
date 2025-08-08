<?php

namespace App\Models;

use App\Models\DetailOrder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use HasFactory;

    use SoftDeletes;

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

    public function detail_order(): HasMany
    {
        return $this->hasMany(DetailOrder::class);
    }
}
