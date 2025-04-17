<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroomingPackage extends Model
{
    protected $table = 'grooming_packages';
    //
    protected $fillable = ['name', 'grooming_id'];

    public function grooming()
    {
        return $this->belongsTo(Grooming::class);
    }
}
