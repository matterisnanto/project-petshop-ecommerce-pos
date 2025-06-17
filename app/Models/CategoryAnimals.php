<?php

namespace App\Models;

use App\Models\Animals;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryAnimals extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'category_animals';
    //
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function animals(): HasMany
    {
        return $this->hasMany(Animals::class);
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
}
