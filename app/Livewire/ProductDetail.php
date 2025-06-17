<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

#[Title('Product - Cindy Petshop')]
class ProductDetail extends Component
{
    public Product $product;
    public int $quantity = 1;
    public $cartItemCount = 0;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->updateCartItemCount();
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        // Validate quantity doesn't exceed available stock
        if ($this->quantity > $this->product->stock) {
            toastr()->error('Insufficient stock, only ' . $this->product->stock . ' available ');
            return;
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$this->product->id])) {
            $newQuantity = $cart[$this->product->id]['quantity'] + $this->quantity;

            // Check if the new total quantity exceeds stock
            if ($newQuantity > $this->product->stock) {
                toastr()->error('Cannot add more than available stock. Current in cart : ' .
                    $cart[$this->product->id]['quantity'] . ', available : ' . $this->product->stock);
                return;
            }

            $cart[$this->product->id]['quantity'] = $newQuantity;
            $cart[$this->product->id]['total_weight'] = $newQuantity * $this->product->weight;
        } else {
            $cart[$this->product->id] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->selling_price,
                'image' => $this->product->image_url ?: 'https://via.placeholder.com/300',
                'weight' => $this->product->weight,
                'quantity' => $this->quantity,
                'total_weight' => $this->quantity * $this->product->weight
            ];
        }

        Session::put('cart', $cart);
        $this->updateCartItemCount();
        $this->dispatch('cartUpdated');
        toastr()->success($this->product->name . ' successfully added to cart');
    }

    protected function updateCartItemCount()
    {
        $cart = Session::get('cart', []);
        $this->cartItemCount = array_sum(array_column($cart, 'quantity'));
    }

    public function render()
    {
        $this->product->load('photos');

        return view('livewire.pages.product-detail', [
            'product' => $this->product
        ]);
    }
}
