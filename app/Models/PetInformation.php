<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class PetInformation extends Model
{
    protected $table = 'pet_information';
    //
    protected $fillable = ['order_id', 'name', 'category_animals_id', 'age', 'photo', 'description', 'check_in', 'check_out', 'days'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function posTransaction()
    {
        return $this->belongsTo(PosTransaction::class);
    }

    public function categoryAnimal()
    {
        return $this->belongsTo(CategoryAnimals::class);
    }

    // app/Models/PetInformation.php
    protected static function booted()
    {
        static::saving(function ($petInfo) {
            if ($petInfo->check_in && $petInfo->check_out) {
                $petInfo->days = Carbon::parse($petInfo->check_in)
                    ->diffInDays(Carbon::parse($petInfo->check_out));
            }
        });
    }
}
