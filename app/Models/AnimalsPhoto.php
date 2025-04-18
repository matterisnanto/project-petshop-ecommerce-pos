<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalsPhoto extends Model
{
    protected $table = 'animals_photos';
    //
    protected $fillable = ['photo', 'animals_id'];

    public function animals()
    {
        return $this->belongsTo(Animals::class);
    }
}
