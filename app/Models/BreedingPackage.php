<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreedingPackage extends Model
{
    protected $table = 'breeding_packages';
    //
    protected $fillable = ['name', 'price', 'description', 'breeding_id'];

    public function breedings()
    {
        return $this->belongsTo(Breeding::class);
    }
}
