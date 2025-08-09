<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\OlshopTransaction;

#[Title('Order Confirmation - CindyPetshop')]
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
