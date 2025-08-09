<?php

namespace App\Livewire;

use App\Models\Breeds;
use App\Models\Animals;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\CategoryAnimals;


#[Title('Animals - CindyPetshop')]
class AnimalsList extends Component
{
    use WithPagination;

    public $sortBy = 'default';
    public $selectedBreeds = [];
    public $selectedCategoryAnimal = [];
    public $minPrice = 0;
    public $maxPrice = 5000000;
    public $cartItemCount = 0;
    public $categoryAnimalName = 'All Animals';

    protected $queryString = [
        'sortBy' => ['except' => 'default'],
        'selectedBreeds' => ['except' => [], 'as' => 'breeds'],
        'selectedCategoryAnimal' => ['except' => [], 'as' => 'categoryAnimal'],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 5000000],
    ];

    public function mount()
    {
        $this->minPrice = request('minPrice', 0);
        $this->maxPrice = request('maxPrice', 5000000);
        $this->selectedBreeds = request('breeds', []);
        $this->selectedCategoryAnimal = request('categoryAnimal', []);
        $this->sortBy = request('sortBy', 'default');

        if (!empty($this->selectedCategoryAnimal)) {
            $categoryAnimals = CategoryAnimals::find($this->selectedCategoryAnimal[0]);
            $this->categoryAnimalName = $categoryAnimals ? $categoryAnimals->name : 'All Animals';
        }
    }

    public function render()
    {
        $query = Animals::where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['categoryAnimals', 'breeds']);

        $filteredQuery = (clone $query)
            ->when($this->sortBy === 'price_asc', fn($q) => $q->orderBy('selling_price', 'asc'))
            ->when($this->sortBy === 'price_desc', fn($q) => $q->orderBy('selling_price', 'desc'))
            ->when($this->sortBy === 'popular', fn($q) => $q->where('is_popular', true))
            ->when(!empty($this->selectedBreeds), fn($q) => $q->whereIn('breeds_id', $this->selectedBreeds))
            ->when(!empty($this->selectedCategoryAnimal), fn($q) => $q->whereIn('category_animals_id', $this->selectedCategoryAnimal))
            ->when($this->minPrice > 0 || $this->maxPrice < 5000000, fn($q) => $q->whereBetween('selling_price', [$this->minPrice, $this->maxPrice]));

        $animals = $filteredQuery->paginate(12);

        $breeds = Breeds::select('id', 'name')
            ->withCount(['animals' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0);
            }])
            ->whereHas('animals', function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0);
            })
            ->orderBy('name')
            ->get();

        $category = CategoryAnimals::select('id', 'name')
            ->withCount(['animals' => function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0);
            }])
            ->whereHas('animals', function ($query) {
                $query->where('is_active', true)
                    ->where('stock', '>', 0);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.pages.animals-list', [
            'animals' => $animals,
            'breeds' => $breeds,
            'category' => $category,
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
    }

    public function resetFilters()
    {
        $this->selectedBreeds = [];
        $this->selectedCategoryAnimal = [];
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

    public function toggleBreed($breedId)
    {
        if (in_array($breedId, $this->selectedBreeds)) {
            $this->selectedBreeds = array_diff($this->selectedBreeds, [$breedId]);
        } else {
            $this->selectedBreeds[] = $breedId;
        }
        $this->resetPage();
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategoryAnimal)) {
            $this->selectedCategoryAnimal = array_diff($this->selectedCategoryAnimal, [$categoryId]);
        } else {
            $this->selectedCategoryAnimal[] = $categoryId;
        }
        $this->resetPage();
    }
}
