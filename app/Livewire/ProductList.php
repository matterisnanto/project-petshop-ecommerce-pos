<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;

class ProductList extends Component
{
    use WithPagination;

    public $sortBy = 'default';
    public $selectedBrands = [];
    public $selectedCategories = [];
    public $minPrice = 0;
    public $maxPrice = 5000000;
    public $showFilters = false;
    public $cartItemCount = 0;

    protected $queryString = [
        'sortBy' => ['except' => 'default'],
        'selectedBrands' => ['except' => [], 'as' => 'brands'],
        'selectedCategories' => ['except' => [], 'as' => 'categories'],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 5000000],
    ];

    public function mount()
    {
        $this->minPrice = request('minPrice', 0);
        $this->maxPrice = request('maxPrice', 5000000);
        $this->selectedBrands = request('brands', []);
        $this->selectedCategories = request('categories', []);
        $this->sortBy = request('sortBy', 'default');
        $this->updateCartItemCount();
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->where('stock', '>', 5)
            ->with(['category', 'brand']);

        $filteredQuery = (clone $query)
            ->when($this->sortBy === 'price_asc', fn($q) => $q->orderBy('selling_price', 'asc'))
            ->when($this->sortBy === 'price_desc', fn($q) => $q->orderBy('selling_price', 'desc'))
            ->when($this->sortBy === 'popular', fn($q) => $q->where('is_popular', true))
            ->when(!empty($this->selectedBrands), fn($q) => $q->whereIn('brand_id', $this->selectedBrands))
            ->when(!empty($this->selectedCategories), fn($q) => $q->whereIn('category_id', $this->selectedCategories))
            ->when($this->minPrice > 0 || $this->maxPrice < 5000000, fn($q) => $q->whereBetween('selling_price', [$this->minPrice, $this->maxPrice]));

        $products = $filteredQuery->paginate(12);

        $brands = Brand::select('id', 'name')
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 5);
            }])
            ->whereHas('products', function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 5);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::select('id', 'name')
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 5);
            }])
            ->whereHas('products', function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 5);
            })
            ->orderBy('name')
            ->get();

        return view('pages.product-list', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'currentSort' => $this->sortBy
        ]);
    }

    public function sort($type)
    {
        $this->sortBy = $type;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->showFilters = false;
    }

    public function resetFilters()
    {
        $this->selectedBrands = [];
        $this->selectedCategories = [];
        $this->minPrice = 0;
        $this->maxPrice = 5000000;
        $this->sortBy = 'default';
        $this->resetPage();
    }

    public function updatedMinPrice($value)
    {
        if ($value > $this->maxPrice) {
            $this->minPrice = $this->maxPrice;
        }
        $this->resetPage();
    }

    public function updatedMaxPrice($value)
    {
        if ($value < $this->minPrice) {
            $this->maxPrice = $this->minPrice;
        }
        $this->resetPage();
    }

    public function toggleBrand($brandId)
    {
        if (in_array($brandId, $this->selectedBrands)) {
            $this->selectedBrands = array_diff($this->selectedBrands, [$brandId]);
        } else {
            $this->selectedBrands[] = $brandId;
        }
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += 1;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'image' => $product->image_url ?: 'https://via.placeholder.com/300',
                'quantity' => 1
            ];
        }

        Session::put('cart', $cart);
        $this->updateCartItemCount();
        $this->dispatch('cartUpdated');
    }

    protected function updateCartItemCount()
    {
        $cart = Session::get('cart', []);
        $this->cartItemCount = array_sum(array_column($cart, 'quantity'));
    }
}
