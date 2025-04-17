<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grooming extends Model
{
    protected $table = 'groomings';
    //
    protected $fillable = ['name', 'slug', 'category_animals_id', 'category_grooming_id', 'photo', 'description', 'stock', 'purchase_price', 'selling_price', 'is_active'];

    public function categoryanimals()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }


    public function categorygrooming()
    {
        return $this->belongsTo(CategoryGrooming::class);
    }
}
