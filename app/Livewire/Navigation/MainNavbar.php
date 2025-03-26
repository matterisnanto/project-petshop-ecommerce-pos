<?php

namespace App\Livewire\Navigation;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class MainNavbar extends Component
{

    public $cartItems = [];
    public $total = 0;
    public $itemCount = 0;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $this->cartItems = Session::get('cart', []);
        $this->calculateTotal();
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));
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

    public function updateQuantity($productId, $quantity)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $quantity = max(1, (int)$quantity);
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
