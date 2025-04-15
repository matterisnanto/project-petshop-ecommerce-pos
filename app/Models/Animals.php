<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animals extends Model
{
    protected $table = 'animalss';
    //
    protected $fillable = ['name', 'slug', 'category_animal_id', 'breeds_id', 'age', 'weight', 'gender', 'health_status', 'vaccination_status', 'description', 'purchase_price', 'selling_price', 'is_active'];
    
    public function categoryanimal() {
        return $this->belongsTo(CategoryAnimal::class);
    }

    
    public function breeds() {
        return $this->belongsTo(Breeds::class);
    }

    
    public function purchaseprice() {
        return $this->belongsTo(PurchasePrice::class);
    }

    
    public function sellingprice() {
        return $this->belongsTo(SellingPrice::class);
    }

}
