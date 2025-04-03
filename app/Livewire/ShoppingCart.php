<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $itemCount = 0;
    public $subtotal = 0;
    public $savings = 0;
    public $voucherCode = '';

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $this->cartItems = Session::get('cart', []);
        $this->calculateTotals();
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));
    }

    public function incrementQuantity($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $product = Product::find($productId);

            if ($cart[$productId]['quantity'] < $product->stock) {
                $cart[$productId]['quantity']++;
                Session::put('cart', $cart);
                $this->updateCart();
                $this->dispatch('cartUpdated');
                toastr()->success('Quantity increased successfully');
            } else {
                toastr()->error('Insufficient stock, only available ' . $product->stock);
            }
        }
    }

    public function decrementQuantity($productId)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                Session::put('cart', $cart);
                $this->updateCart();
                $this->dispatch('cartUpdated');

                // Optional: Add success message
                toastr()->success('Quantity decreased successfully');
            } else {
                $this->removeItem($productId);
            }
        } else {
            // Optional: Add error message if product not found in cart
            toastr()->error('Product not found in cart');
        }
    }

    public function removeItem($productId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productId])) {
            $product = Product::find($productId);
            unset($cart[$productId]);
            Session::put('cart', $cart);
            toastr()->warning($product->name . ' has been removed from cart');
            $this->updateCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function updateItemQuantity($productId, $quantity)
    {
        $quantity = max(1, (int)$quantity);
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $product = Product::find($productId);
            $quantity = min($quantity, $product->stock);

            $cart[$productId]['quantity'] = $quantity;
            Session::put('cart', $cart);
            $this->updateCart();
            toastr()->success('Quantity has been updated');
            $this->dispatch('cartUpdated');

            if ($quantity == $product->stock) {
                toastr()->error('Quantity reached available stock limit');
            }
        }
    }

    public function applyVoucher()
    {
        // Implement your voucher logic here
        // For example:
        if ($this->voucherCode === 'DISCOUNT10') {
            $this->savings = $this->subtotal * 0.1; // 10% discount
            session()->flash('success', 'Voucher applied successfully!');
        } else {
            $this->savings = 0;
            session()->flash('error', 'Invalid voucher code');
        }

        $this->calculateTotals();
    }

    protected function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->total = $this->subtotal - $this->savings;
    }

    // public function checkout()
    // {
    //     // Implement your checkout logic here
    //     return redirect()->route('checkout');
    // }

    public function render()
    {
        return view('pages.shopping-cart');
    }
}
