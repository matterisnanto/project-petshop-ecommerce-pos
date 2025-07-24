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
    public function created(Order $detail_order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($detail_order->purchases_id !== null) {
            return;
        }

        if ($detail_order->type === 'product' && $detail_order->product_id) {
            $product = Product::find($detail_order->product_id);
            if ($product) {
                $product->decrement('stock', $detail_order->quantity);
            }
        } elseif ($detail_order->type === 'animal' && $detail_order->animals_id) {
            $animal = Animals::find($detail_order->animals_id);
            if ($animal) {
                $animal->decrement('stock', $detail_order->quantity);

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
    public function updated(Order $detail_order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($detail_order->purchases_id !== null) {
            return;
        }

        // Handle product detail_order update
        if ($detail_order->type === 'product' && $detail_order->product_id) {
            $product = Product::find($detail_order->product_id);
            if ($product) {
                $originalQuantity = $detail_order->getOriginal('quantity');
                $newQuantity = $detail_order->quantity;

                if ($originalQuantity != $newQuantity) {
                    $product->increment('stock', $originalQuantity);
                    $product->decrement('stock', $newQuantity);
                }
            }
        }
        // Handle animal detail_order update
        elseif ($detail_order->type === 'animal' && $detail_order->animals_id) {
            $animal = Animals::find($detail_order->animals_id);
            if ($animal) {
                $originalQuantity = $detail_order->getOriginal('quantity');
                $newQuantity = $detail_order->quantity;

                if ($originalQuantity != $newQuantity) {
                    $animal->increment('stock', $originalQuantity);
                    $animal->decrement('stock', $newQuantity);

                    // Update status aktif berdasarkan stok terbaru
                    $animal->update([
                        'is_active' => $animal->stock > 0
                    ]);
                }

                // Jika animals_id berubah
                if ($detail_order->isDirty('animals_id')) {
                    $originalAnimal = Animals::find($detail_order->getOriginal('animals_id'));
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
    public function deleted(Order $detail_order): void
    {
        // Hanya proses jika purchases_id kosong
        if ($detail_order->purchases_id !== null) {
            return;
        }

        if ($detail_order->type === 'product' && $detail_order->product_id) {
            $product = Product::find($detail_order->product_id);
            if ($product) {
                $product->increment('stock', $detail_order->quantity);
            }
        } elseif ($detail_order->type === 'animal' && $detail_order->animals_id) {
            $animal = Animals::find($detail_order->animals_id);
            if ($animal) {
                $animal->increment('stock', $detail_order->quantity);

                // Aktifkan kembali hewan jika stok menjadi > 0
                if ($animal->stock > 0) {
                    $animal->update(['is_active' => true]);
                }
            }
        }
    }
}
