<?php

namespace App\Models;

use App\Models\Animals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnimalsPhoto extends Model
{
    use HasFactory;

    protected $table = 'animals_photos';

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
