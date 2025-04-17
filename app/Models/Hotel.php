<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $table = 'hotels';
    //
    protected $fillable = ['name', 'category_animals_id', 'description', 'price_per_day', 'capacity', 'thumbnail', 'is_active'];

    public function categoryanimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }
}
