<?php

namespace App\Models;

use App\Models\Hotel;
use App\Models\Animals;
use App\Models\Product;
use App\Models\Breeding;
use App\Models\Grooming;
use App\Models\Purchase;
use App\Models\PetInformation;
use App\Models\PosTransaction;
use App\Models\OlshopTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailOrder extends Model
{
    use HasFactory;

    protected $table = 'detail_order';

    protected $fillable = ['pos_transaction_id', 'olshop_transaction_id', 'purchases_id', 'type', 'product_id', 'animals_id', 'grooming_id', 'hotel_id', 'breeding_id', 'quantity', 'unit_price'];


    public function posTransaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }
    public function olshopTransaction(): BelongsTo
    {
        return $this->belongsTo(OlshopTransaction::class, 'olshop_transaction_id');
    }
    public function purchases(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchases_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function animal()
    {
        return $this->belongsTo(Animals::class, 'animals_id');
    }
    public function grooming()
    {
        return $this->belongsTo(Grooming::class, 'grooming_id');
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function breeding()
    {
        return $this->belongsTo(Breeding::class, 'breeding_id');
    }


    public function petInformation()
    {
        return $this->hasMany(PetInformation::class, 'detail_order_id');
    }
}
