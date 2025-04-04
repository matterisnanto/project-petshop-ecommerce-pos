<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\PromoCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ShoppingCart extends Component
{
    public $cartItems = [];
    public $total = 0;
    public $itemCount = 0;
    public $subtotal = 0;
    public $savings = 0;
    public $promoCode = '';
    public $appliedPromoCode = null;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        // When component mounts, check if there are totals in session
        $cartTotals = Session::get('cart_totals', []);

        $this->updateCart();

        // If totals exist in session, use them
        if (!empty($cartTotals)) {
            $this->subtotal = $cartTotals['subtotal'] ?? 0;
            $this->total = $cartTotals['total'] ?? 0;
            $this->savings = $cartTotals['savings'] ?? 0;
            $this->itemCount = $cartTotals['itemCount'] ?? 0;
            $this->appliedPromoCode = $cartTotals['appliedPromoCode'] ?? null;

            // If coming back from checkout, reset promo code input
            if (!$this->appliedPromoCode) {
                $this->promoCode = '';
            }
        }
    }

    public function updateCart()
    {
        $this->cartItems = Session::get('cart', []);
        $this->calculateTotals();
        $this->itemCount = array_sum(array_column($this->cartItems, 'quantity'));

        // Store the calculated totals in session
        Session::put('cart_totals', [
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'savings' => $this->savings,
            'itemCount' => $this->itemCount,
            'appliedPromoCode' => $this->appliedPromoCode
        ]);
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

            if (empty($cart)) {
                Session::forget('cart_totals');
            }

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

    public function applyPromoCode()
    {
        // Reset previous promo code application
        $this->savings = 0;
        $this->appliedPromoCode = null;

        if (empty($this->promoCode)) {
            session()->flash('error', 'Please enter a promo code');
            $this->calculateTotals();
            return;
        }

        $promo = PromoCode::where('code', $this->promoCode)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->first();

        if (!$promo) {
            toastr()->error('Invalid or expired promo code');
            $this->calculateTotals();
            return;
        }

        $this->appliedPromoCode = $promo->code;
        $this->savings = $promo->discount_amount;

        toastr()->success('Promo code applied successfully!');
        $this->calculateTotals();
    }

    protected function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->total = max(0, $this->subtotal - $this->savings);

        // Update session totals whenever totals are calculated
        Session::put('cart_totals', [
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'savings' => $this->savings,
            'itemCount' => $this->itemCount,
            'appliedPromoCode' => $this->appliedPromoCode
        ]);
    }

    // public function checkout()
    // {
    //     // Implement your checkout logic here
    //     return redirect()->route('/shopping-cart/checkout');
    // }

    public function render()
    {
        return view('pages.shopping-cart');
    }
}
