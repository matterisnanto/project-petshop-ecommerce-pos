<?php

namespace App\Models;

use App\Models\DetailOrder;
use App\Models\PosTransaction;
use Illuminate\Support\Carbon;
use App\Models\CategoryAnimals;
use Illuminate\Database\Eloquent\Model;

class PetInformation extends Model
{
    protected $table = 'pet_information';
    //
    protected $fillable = ['detail_order_id', 'name', 'category_animals_id', 'age', 'photo', 'description', 'check_in', 'check_out', 'days', 'on_petshop'];

    public function detail_order()
    {
        return $this->belongsTo(DetailOrder::class);
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
                $days = Carbon::parse($petInfo->check_in)
                    ->diffInDays(Carbon::parse($petInfo->check_out));
                $petInfo->days = max(1, $days);

                // Update the associated detail_order's quantity if this is a hotel or breeding service
                if ($petInfo->detail_order && in_array($petInfo->detail_order->type, ['hotel', 'breeding'])) {
                    $petInfo->detail_order->quantity = max(1, $days);
                    $petInfo->detail_order->save();
                }
            }
        });
    }
}
