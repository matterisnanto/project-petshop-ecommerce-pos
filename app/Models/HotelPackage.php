<?php

namespace App\Models;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Model;

class HotelPackage extends Model
{
    protected $table = 'hotel_packages';
    //
    protected $fillable = ['name', 'price', 'description', 'hotel_id'];

    public function hotels()
    {
        return $this->belongsTo(Hotel::class);
    }
}
