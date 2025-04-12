<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OlshopTransaction;
use App\Models\Order;

class OrderConfirmation extends Component
{
    public $transactionId;
    public $transaction;
    public $products;

    public function mount($transaction_id = null)
    {

        if ($transaction_id) {
            $this->transactionId = $transaction_id;
            $this->transaction = OlshopTransaction::where('trx_id', $transaction_id)
                ->with(['paymentMethod', 'orders.product'])
                ->first();
            if (!$this->transaction) {
                abort(404);
            }
        } else {
            // Jika tidak ada transaction_id, coba dapatkan dari session atau redirect
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.pages.order-confirmation', [
            'transaction' => $this->transaction
        ]);
    }
}
