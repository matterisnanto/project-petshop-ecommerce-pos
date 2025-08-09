<?php

namespace App\Livewire;

use App\Models\Brands;
use App\Models\Categories;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\CategoryAnimals;
use Illuminate\Support\Facades\Session;

#[Title('Product - CindyPetshop')]
class ProductList extends Component
{
    use WithPagination;

    public $sortBy = 'default';
    public $selectedBrands = [];
    public $selectedcategory = [];
    public $selectedAnimalcategory = [];
    public $minPrice = 0;
    public $maxPrice = 5000000;
    public $showFilters = false;
    public $cartItemCount = 0;
    public $cartTotalWeight = 0;
    public $categoryName = 'All Products';

    protected $queryString = [
        'sortBy' => ['except' => 'default'],
        'selectedBrands' => ['except' => [], 'as' => 'brand'],
        'selectedcategory' => ['except' => [], 'as' => 'category'],
        'selectedAnimalcategory' => ['except' => [], 'as' => 'animal_category'],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 5000000],
    ];

    public function mount()
    {
        $this->minPrice = request('minPrice', 0);
        $this->maxPrice = request('maxPrice', 5000000);
        $this->selectedBrands = request('brand', []);
        $this->selectedcategory = request('category', []);
        $this->selectedAnimalcategory = request('animal_category', []);
        $this->sortBy = request('sortBy', 'default');

        if (!empty($this->selectedcategory)) {
            $category = Categories::find($this->selectedcategory[0]);
            $this->categoryName = $category ? $category->name : 'All Products';
        }
        // Session::forget('cart_totals');
        $this->updateCartItemCount();
    }

    public function render()
    {
        $query = Product::where('is_active', true)
            ->where('stock', '>', 2)
            ->with(['categories', 'brands']);

        $filteredQuery = (clone $query)
            ->when($this->sortBy === 'price_asc', fn($q) => $q->orderBy('selling_price', 'asc'))
            ->when($this->sortBy === 'price_desc', fn($q) => $q->orderBy('selling_price', 'desc'))
            ->when($this->sortBy === 'popular', fn($q) => $q->where('is_popular', true))
            ->when(!empty($this->selectedBrands), fn($q) => $q->whereIn('brands_id', $this->selectedBrands))
            ->when(!empty($this->selectedcategory), fn($q) => $q->whereIn('categories_id', $this->selectedcategory))
            ->when(!empty($this->selectedAnimalcategory), fn($q) => $q->whereIn('category_animals_id', $this->selectedAnimalcategory))
            ->when($this->minPrice > 0 || $this->maxPrice < 5000000, fn($q) => $q->whereBetween('selling_price', [$this->minPrice, $this->maxPrice]));

        $products = $filteredQuery->paginate(12);

        $brand = Brands::select('id', 'name')
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

        $category = Categories::select('id', 'name')
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

        $animalcategory = CategoryAnimals::select('id', 'name')
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

        return view('livewire.pages.product-list', [
            'products' => $products,
            'brands' => $brand,
            'categories' => $category,
            'animalcategory' => $animalcategory,
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
        $this->selectedcategory = [];
        $this->selectedAnimalcategory = [];
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
        if (in_array($categoryId, $this->selectedcategory)) {
            $this->selectedcategory = array_diff($this->selectedcategory, [$categoryId]);
        } else {
            $this->selectedcategory[] = $categoryId;
        }
        $this->resetPage();
    }

    public function toggleAnimalCategory($animalCategoryId)
    {
        if (in_array($animalCategoryId, $this->selectedAnimalcategory)) {
            $this->selectedAnimalcategory = array_diff($this->selectedAnimalcategory, [$animalCategoryId]);
        } else {
            $this->selectedAnimalcategory[] = $animalCategoryId;
        }
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            // Check if adding one more would exceed stock
            if (($cart[$productId]['quantity'] + 1) > $product->stock) {
                toastr()->error('Cannot add more than available stock. Current in cart: ' .
                    $cart[$productId]['quantity'] . ', available: ' . $product->stock);
                return;
            }

            $cart[$productId]['quantity'] += 1;
            $cart[$productId]['total_weight'] = $cart[$productId]['quantity'] * $product->weight;
        } else {
            // For new item, check if at least 1 is available
            if ($product->stock < 1) {
                toastr()->error('Insufficient stock for ' . $product->name);
                return;
            }

            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->selling_price,
                'image' => $product->image_url ?: 'https://via.placeholder.com/300',
                'weight' => $product->weight,
                'quantity' => 1,
                'total_weight' => $product->weight // Tambahkan total_weight per item
            ];
        }

        Session::put('cart', $cart);
        $this->updateCartItemCount();
        $this->dispatch('cartUpdated');
        toastr()->success($product->name . ' successfully added to cart');
    }

    protected function updateCartItemCount()
    {
        $cart = Session::get('cart', []);
        $this->cartItemCount = array_sum(array_column($cart, 'quantity'));
        $this->cartTotalWeight = array_sum(array_column($cart, 'total_weight'));
    }

    public function updating($name, $value)
    {
        // Reset page ketika filter/sort diubah
        if (in_array($name, ['selectedBrands', 'selectedcategory', 'selectedAnimalcategory', 'minPrice', 'maxPrice', 'sortBy'])) {
            $this->resetPage();
        }
    }
}
