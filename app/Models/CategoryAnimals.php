<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAnimals extends Model
{
    protected $table = 'categoryanimalss';
    //
    protected $fillable = ['name', 'slug', 'description', 'icon'];
}
