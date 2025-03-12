<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Order;

class OrderObserver
{
    //
    public function created(Order $order): void
    {
        $product = Product::find($order->product_id);
        $product->decrement('stock', $order->quantity); //mengurangi stok diorder prodak sejumlah kuantitas yg diorder
    }

    public function updated(Order $order): void
    {
        $product = Product::find($order->product_id);
        $originalQuantity = $order->getOriginal('quantity');
        $newQuantity = $order->quantity;

        if ($originalQuantity != $newQuantity) {
            $product->increment('stock', $originalQuantity);
            $product->decrement('stock', $newQuantity);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $product = Product::find($order->product_id);
        $product->increment('stock', $order->quantity);
    }
}
