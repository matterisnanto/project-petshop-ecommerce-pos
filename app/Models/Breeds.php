<?php

namespace App\Models;

use App\Models\Animals;
use Illuminate\Support\Str;
use App\Models\CategoryAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Breeds extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'breeds';
    //
    protected $fillable = ['category_animals_id', 'name', 'slug'];

    public function animals()
    {
        return $this->hasMany(Animals::class);
    }

    public function categoryAnimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
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
