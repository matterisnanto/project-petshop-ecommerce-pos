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
    public $totalWeight = 0;

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
            $this->totalWeight = $cartTotals['totalWeight'] ?? 0;

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
        $this->totalWeight = $this->calculateTotalWeight();

        // Store the calculated totals in session
        Session::put('cart_totals', [
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'savings' => $this->savings,
            'itemCount' => $this->itemCount,
            'totalWeight' => $this->totalWeight,
            'appliedPromoCode' => $this->appliedPromoCode
        ]);
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
            $product = Product::find($productId);

            if ($cart[$productId]['quantity'] < $product->stock) {
                $cart[$productId]['quantity']++;
                $cart[$productId]['total_weight'] = $cart[$productId]['quantity'] * $product->weight;
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
            $product = Product::find($productId); // Tambahkan ini untuk mendapatkan data produk

            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                $cart[$productId]['total_weight'] = $cart[$productId]['quantity'] * $product->weight; // Update berat
                Session::put('cart', $cart);
                $this->updateCart();
                $this->dispatch('cartUpdated');
                toastr()->success('Quantity decreased successfully');
            } else {
                $this->removeItem($productId);
            }
        } else {
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
            $cart[$productId]['total_weight'] = $quantity * $product->weight;
            Session::put('cart', $cart);
            $this->updateCart();
            $this->dispatch('cartUpdated');

            if ($quantity == $product->stock) {
                toastr()->error('Quantity reached available stock limit');
            } else {
                toastr()->success('Quantity updated successfully');
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
            'totalWeight' => $this->totalWeight,
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
