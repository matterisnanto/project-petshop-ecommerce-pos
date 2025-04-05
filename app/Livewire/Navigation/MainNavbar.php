<?php

namespace App\Livewire\Navigation;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class MainNavbar extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $itemCount = 0;
    public $totalWeight = 0;
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
        $this->totalWeight = $this->calculateTotalWeight();
    }

    protected function calculateTotalWeight()
    {
        $totalWeight = 0;
        foreach ($this->cartItems as $item) {
            $totalWeight += $item['weight'] * $item['quantity'];
        }
        return $totalWeight;
    }

    public function incrementQuantity($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            // Ambil stok produk dari database
            $product = Product::find($productId);

            // Periksa apakah stok mencukupi
            if ($cart[$productId]['quantity'] < $product->stock) {
                $cart[$productId]['quantity']++;
                $cart[$productId]['total_weight'] = $cart[$productId]['quantity'] * $product->weight;
                Session::put('cart', $cart);
                $this->updateCart();
            } else {
                // Jika stok tidak mencukupi, Anda bisa menambahkan pesan error
                toastr()->error('Insufficient stock, only available ' . $product->stock);
            }
        }
        $this->dispatch('cartUpdated');
    }

    public function decrementQuantity($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $product = Product::find($productId);

            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                $cart[$productId]['total_weight'] = $cart[$productId]['quantity'] * $product->weight; // Update berat
                Session::put('cart', $cart);
                $this->updateCart();
            }
        }
    }

    public function removeItem($productId)
    {
        $cart = Session::get('cart', []);
        $product = Product::find($productId);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);

            if (empty($cart)) {
                Session::forget('cart_totals');
            }

            toastr()->warning($product->name . ' Has been removed from cart');
            $this->updateCart();
        }
    }

    public function updateItemQuantity($productId, $quantity)
    {
        $quantity = max(1, (int)$quantity);
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            // Ambil stok produk dari database
            $product = Product::find($productId);

            // Pastikan quantity tidak melebihi stok
            $quantity = min($quantity, $product->stock);

            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['total_weight'] = $quantity * $product->weight;
            Session::put('cart', $cart);
            $this->updateCart();

            if ($quantity == $product->stock) {
                session()->flash('error', 'Jumlah mencapai batas stok tersedia');
            }
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
