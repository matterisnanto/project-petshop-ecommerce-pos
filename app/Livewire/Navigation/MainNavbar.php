<?php

namespace App\Http\Livewire\Navigation;

use Livewire\Component;

class MainNavbar extends Component
{
    public $cartItems = [
        ['name' => 'Apple iPhone 15', 'price' => 599, 'qty' => 1],
        ['name' => 'Apple iPad Air', 'price' => 499, 'qty' => 1],
        ['name' => 'Apple Watch SE', 'price' => 598, 'qty' => 2],
        ['name' => 'Sony Playstation 5', 'price' => 799, 'qty' => 1],
        ['name' => 'Apple iMac 20"', 'price' => 8997, 'qty' => 3],
    ];

    public $totalItems;
    public $subtotal;

    public function mount()
    {
        $this->calculateCart();
    }

    public function calculateCart()
    {
        $this->totalItems = array_sum(array_column($this->cartItems, 'qty'));
        $this->subtotal = array_reduce($this->cartItems, function ($carry, $item) {
            return $carry + ($item['price'] * $item['qty']);
        }, 0);
    }

    public function removeItem($index)
    {
        unset($this->cartItems[$index]);
        $this->cartItems = array_values($this->cartItems);
        $this->calculateCart();
    }

    public function render()
    {
        return view('livewire.navigation.main-navbar');
    }
}
