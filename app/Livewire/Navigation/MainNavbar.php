<?php

namespace App\Livewire\Navigation;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class MainNavbar extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $itemCount = 0;
    public $activeRoute = '';

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
        $this->activeRoute = request()->path();
    }

    public function updateCart()
    {
        $this->cartItems = Session::get('cart', []);
        $this->calculateTotal();
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));
    }

    public function incrementQuantity($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            Session::put('cart', $cart);
            $this->updateCart();
        }
    }

    public function decrementQuantity($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId]) && $cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity']--;
            Session::put('cart', $cart);
            $this->updateCart();
        }
    }

    public function removeItem($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            $this->updateCart();
        }
    }

    public function updateItemQuantity($productId, $quantity)
    {
        $quantity = max(1, (int)$quantity);
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            Session::put('cart', $cart);
            $this->updateCart();
        }
    }

    protected function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cartItems as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    public function render()
    {
        return view('livewire.navigation.main-navbar');
    }
}
