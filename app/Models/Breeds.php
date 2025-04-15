<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breeds extends Model
{
    protected $table = 'breedss';
    //
    protected $fillable = ['category_animals_id', 'name', 'slug'];
    
    public function categoryanimals() {
        return $this->belongsTo(CategoryAnimals::class);
    }

}
