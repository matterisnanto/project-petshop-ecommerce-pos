<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Grooming extends Model
{
    protected $table = 'groomings';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'category_grooming_id', 'photo', 'description', 'stock', 'purchase_price', 'selling_price', 'is_active'];

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }

    public function categoryGrooming()
    {
        return $this->belongsTo(CategoryGrooming::class,);
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
}