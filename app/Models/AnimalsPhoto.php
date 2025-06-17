<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalsPhoto extends Model
{
    use HasFactory;

    protected $table = 'animals_photos';
    //
    protected $fillable = ['photo', 'animals_id'];

    public function animals()
    {
        return $this->belongsTo(Animals::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? url('storage/', $this->photo) : null;
    }
}
