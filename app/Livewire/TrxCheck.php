<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\OlshopTransaction;
use Illuminate\Support\Facades\Session;

#[Title('TRX Check - CindyPetshop')]
class TrxCheck extends Component
{
    public $transactionId;
    public $transaction;
    public $error;
    public $showErrorModal = false;

    public function search()
    {
        $this->reset(['transaction', 'error', 'showErrorModal']);

        $this->validate([
            'transactionId' => 'required|string|min:11',
        ], [
            'transactionId.required' => 'Please enter a transaction ID',
            'transactionId.min' => 'Transaction ID must be at least 11 characters'
        ]);

        $this->transaction = OlshopTransaction::with([
            'orders.product',
            'paymentmethod',
            'promocode'
        ])
            ->where('trx_id', $this->transactionId)
            ->first();

        if (!$this->transaction) {
            $this->error = 'Transaction not found. Please check your transaction ID.';
            $this->showErrorModal = true;
            return;
        }

        // Additional validation checks
        if (!$this->transaction->paymentmethod) {
            $this->error = 'Transaction found but payment method information is missing.';
            $this->showErrorModal = true;
            $this->transaction = null;
            return;
        }

        if ($this->transaction->orders->isEmpty()) {
            $this->error = 'Transaction found but order items are missing.';
            $this->showErrorModal = true;
            $this->transaction = null;
            return;
        }
    }

    public function closeErrorModal()
    {
        $this->showErrorModal = false;
    }

    public function render()
    {
        return view('livewire.pages.trx-check', [
            'transaction' => $this->transaction,
            'error' => $this->error,
            'showErrorModal' => $this->showErrorModal,
        ]);
    }
}
