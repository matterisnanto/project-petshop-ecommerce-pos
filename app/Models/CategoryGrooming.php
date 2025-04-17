<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryGrooming extends Model
{
    protected $table = 'category_groomings';
    //
    protected $fillable = ['name', 'slug', 'description', 'photo'];
}
