<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    // Table name for this model
    protected $table = 'products';
    //
    protected $fillable = ['name', 'slug', 'barcode', 'thumbnail', 'about', 'price', 'is_active', 'is_popular', 'stock', 'category_id', 'brand_id', 'supplier_id'];
    

    
    public function category() {
        return $this->belongsTo(Category::class);
    }

    
    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    
    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

}