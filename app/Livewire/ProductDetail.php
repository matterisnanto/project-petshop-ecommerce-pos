<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        // Load photos tanpa pengurutan khusus
        $this->product->load('photos');

        return view('pages.product-detail', [
            'product' => $this->product
        ])->layout('components.layouts.app');
    }
}
