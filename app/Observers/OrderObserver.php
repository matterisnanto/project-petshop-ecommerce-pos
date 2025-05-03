<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Animals;
use App\Models\Product;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($order->purchases_id !== null) {
            return;
        }

        if ($order->type === 'product' && $order->product_id) {
            $product = Product::find($order->product_id);
            if ($product) {
                $product->decrement('stock', $order->quantity);
            }
        } elseif ($order->type === 'animal' && $order->animals_id) {
            $animal = Animals::find($order->animals_id);
            if ($animal) {
                $animal->decrement('stock', $order->quantity);

                // Nonaktifkan hewan jika stok habis
                if ($animal->stock <= 0) {
                    $animal->update(['is_active' => false]);
                }
            }
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($order->purchases_id !== null) {
            return;
        }

        // Handle product order update
        if ($order->type === 'product' && $order->product_id) {
            $product = Product::find($order->product_id);
            if ($product) {
                $originalQuantity = $order->getOriginal('quantity');
                $newQuantity = $order->quantity;

                if ($originalQuantity != $newQuantity) {
                    $product->increment('stock', $originalQuantity);
                    $product->decrement('stock', $newQuantity);
                }
            }
        }
        // Handle animal order update
        elseif ($order->type === 'animal' && $order->animals_id) {
            $animal = Animals::find($order->animals_id);
            if ($animal) {
                $originalQuantity = $order->getOriginal('quantity');
                $newQuantity = $order->quantity;

                if ($originalQuantity != $newQuantity) {
                    $animal->increment('stock', $originalQuantity);
                    $animal->decrement('stock', $newQuantity);

                    // Update status aktif berdasarkan stok terbaru
                    $animal->update([
                        'is_active' => $animal->stock > 0
                    ]);
                }

                // Jika animals_id berubah
                if ($order->isDirty('animals_id')) {
                    $originalAnimal = Animals::find($order->getOriginal('animals_id'));
                    if ($originalAnimal) {
                        $originalAnimal->increment('stock', $originalQuantity);
                        $originalAnimal->update([
                            'is_active' => $originalAnimal->stock > 0
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($order->purchases_id !== null) {
            return;
        }

        if ($order->type === 'product' && $order->product_id) {
            $product = Product::find($order->product_id);
            if ($product) {
                $product->increment('stock', $order->quantity);
            }
        } elseif ($order->type === 'animal' && $order->animals_id) {
            $animal = Animals::find($order->animals_id);
            if ($animal) {
                $animal->increment('stock', $order->quantity);

                // Aktifkan kembali hewan jika stok menjadi > 0
                if ($animal->stock > 0) {
                    $animal->update(['is_active' => true]);
                }
            }
        }
    }
}
