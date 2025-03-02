<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTransaction extends Model
{
    protected $table = 'postransactions';
    //
    protected $fillable = [
        'name', 
        'email', 
        'gender', 
        'total_price', 
        'note', 
        'payment_method_id', 
        'paid_amount', 
        'change_amount'
    ];
    
    public function paymentmethod() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    

}