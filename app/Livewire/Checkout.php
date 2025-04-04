<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $shippingCost = 0;
    public $total = 0;
    public $savings = 0;

    public function mount()
    {
        $this->cartItems = Session::get('cart', []);

        // Get totals from session instead of calculating them
        $cartTotals = Session::get('cart_totals', []);
        $this->subtotal = $cartTotals['subtotal'] ?? 0;
        $this->savings = $cartTotals['savings'] ?? 0;

        $this->calculateTotals();
    }

    public function dehydrate()
    {
        // When leaving the checkout page, reset promo code and savings
        Session::put('cart_totals', [
            'subtotal' => $this->subtotal,
            'total' => $this->subtotal, // Reset total to subtotal
            'savings' => 0,
            'itemCount' => Session::get('cart_totals.itemCount', 0),
            'appliedPromoCode' => null
        ]);
    }

    protected function calculateTotals()
    {
        // Shipping cost logic remains the same
        $this->shippingCost = 15000; // Contoh biaya pengiriman tetap

        // Calculate total using values from session
        $this->total = $this->subtotal + $this->shippingCost - $this->savings;
    }

    public function render()
    {
        return view('pages.checkout');
    }
}
