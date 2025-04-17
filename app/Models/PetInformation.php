<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetInformation extends Model
{
    protected $table = 'pet_information';
    //
    protected $fillable = ['order_id', 'name', 'age', 'photo', 'description', 'check_in', 'check_out', 'days'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
