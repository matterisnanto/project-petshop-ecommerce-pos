<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $sortBy = 'default'; // default, price_asc, price_desc, popular
    public $selectedBrands = [];
    public $selectedCategories = [];
    public $minPrice = 0;
    public $maxPrice = 7000;
    public $showFilters = false;
    public $activeTab = 'brand';

    protected $queryString = [
        'sortBy' => ['except' => 'default'],
        'selectedBrands' => ['except' => [], 'as' => 'brands'],
        'selectedCategories' => ['except' => [], 'as' => 'categories'],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 7000],
        'activeTab' => ['except' => 'brand'] // Tambahkan ini
    ];

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tab-changed'); // Untuk optimasi lebih lanjut
    }

    public function mount()
    {
        // Initialize from query string if present
        $this->minPrice = request('minPrice', 0);
        $this->maxPrice = request('maxPrice', 10000000);
        $this->selectedBrands = request('brands', []);
        $this->selectedCategories = request('categories', []);
        $this->sortBy = request('sortBy', 'default');
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->sortBy === 'price_asc', function ($query) {
                return $query->orderBy('selling_price', 'asc');
            })
            ->when($this->sortBy === 'price_desc', function ($query) {
                return $query->orderBy('selling_price', 'desc');
            })
            ->when($this->sortBy === 'popular', function ($query) {
                return $query->where('is_popular', true);
            })
            ->when(!empty($this->selectedBrands), function ($query) {
                return $query->whereIn('brand_id', $this->selectedBrands);
            })
            ->when(!empty($this->selectedCategories), function ($query) {
                return $query->whereIn('category_id', $this->selectedCategories);
            })
            ->when($this->minPrice > 0 || $this->maxPrice < 7000, function ($query) {
                return $query->whereBetween('selling_price', [$this->minPrice, $this->maxPrice]);
            })
            ->where('is_active', true)
            ->with(['category', 'brand'])
            ->paginate(12);

        $brands = Brand::withProductsCount()
            ->whereHas('products', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::withProductsCount()
            ->whereHas('products', function ($query) {
                $query->where('is_active', true);
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
        $this->maxPrice = 10000000;
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
}
