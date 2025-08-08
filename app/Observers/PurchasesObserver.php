<?php

namespace App\Observers;

use App\Models\Animals;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;

class PurchasesObserver
{
    /**
     * Handle the Purchases "created" event.
     */
    public function created(Purchase $purchases): void
    {
        // Jika langsung dibuat dengan status received
        if ($purchases->status === 'received') {
            $this->updateInventory($purchases, 'increment');
        }
    }

    /**
     * Handle the Purchases "updated" event.
     */
    public function updated(Purchase $purchases): void
    {
        // Hanya proses jika status berubah
        if (!$purchases->isDirty('status')) {
            return;
        }

        $originalStatus = $purchases->getOriginal('status');
        $newStatus = $purchases->status;

        // Case 1: Dari draft/ordered ke received - TAMBAH stok
        if (in_array($originalStatus, ['draft', 'ordered']) && $newStatus === 'received') {
            $this->updateInventory($purchases, 'increment');
        }
        // Case 2: Dari received ke cancelled - KURANGI stok
        elseif ($originalStatus === 'received' && $newStatus === 'cancelled') {
            $this->updateInventory($purchases, 'decrement');
        }
        // Case 3: Dari cancelled kembali ke received - TAMBAH stok
        elseif ($originalStatus === 'cancelled' && $newStatus === 'received') {
            $this->updateInventory($purchases, 'increment');
        }
    }

    /**
     * Handle the Purchases "deleted" event.
     */
    public function deleted(Purchase $purchases): void
    {
        // Jika purchase dihapus dan statusnya received, kurangi stok
        if ($purchases->status === 'received') {
            $this->updateInventory($purchases, 'decrement');
        }
    }

    /**
     * Handle the Purchases "restored" event.
     */
    public function restored(Purchase $purchases): void
    {
        // Jika purchase direstore dan statusnya received, tambah stok
        if ($purchases->status === 'received') {
            $this->updateInventory($purchases, 'increment');
        }
    }

    /**
     * Update inventory based on purchase items
     */
    protected function updateInventory(Purchase $purchases, string $operation): void
    {
        if (!in_array($operation, ['increment', 'decrement'])) {
            return;
        }

        $orders = $purchases->orders;

        foreach ($orders as $detail_order) {
            try {
                if ($detail_order->type === 'product' && $detail_order->product_id) {
                    Product::where('id', $detail_order->product_id)
                        ->$operation('stock', $detail_order->quantity);
                } elseif ($detail_order->type === 'animal' && $detail_order->animals_id) {
                    Animals::where('id', $detail_order->animals_id)
                        ->$operation('stock', $detail_order->quantity);
                }
            } catch (\Exception $e) {
                Log::error("Failed to {$operation} inventory for detail_order {$detail_order->id}: " . $e->getMessage());
            }
        }
    }
}
