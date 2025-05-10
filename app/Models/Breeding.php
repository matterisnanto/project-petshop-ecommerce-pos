<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Breeding extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'breedings';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'breeds_id', 'photo', 'description', 'stock', 'purchase_price', 'selling_price', 'is_active'];

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }


    public function breeds()
    {
        return $this->belongsTo(Breeds::class);
    }

    public function breedingPackage()
    {
        return $this->hasMany(BreedingPackage::class);
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
