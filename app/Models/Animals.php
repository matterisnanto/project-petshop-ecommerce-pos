<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\AnimalsPhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animals extends Model
{
    use HasFactory;
    protected $table = 'animals';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'breeds_id', 'age', 'weight', 'stock', 'gender', 'health_status', 'vaccination_status', 'description', 'purchase_price', 'selling_price', 'is_active'];

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }

    public function breeds()
    {
        return $this->belongsTo(Breeds::class);
    }

    public function animalsPhotos(): HasMany
    {
        return $this->hasMany(AnimalsPhoto::class, 'animals_id');
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
}
