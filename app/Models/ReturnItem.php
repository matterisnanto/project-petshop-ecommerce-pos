<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'return_items';
    //
    protected $fillable = ['purchase_return_id', 'transaction_return_id', 'type', 'product_id', 'animals_id', 'grooming_id', 'breeding_id', 'hotel_id', 'quantity', 'unit_price', 'reason'];

    public function purchasereturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }


    public function transactionreturn()
    {
        return $this->belongsTo(TransactionReturn::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function animals()
    {
        return $this->belongsTo(Animals::class);
    }


    public function grooming()
    {
        return $this->belongsTo(Grooming::class);
    }


    public function breeding()
    {
        return $this->belongsTo(Breeding::class);
    }


    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
