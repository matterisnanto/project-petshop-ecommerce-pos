<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Breeding;
use Livewire\Attributes\Title;

#[Title('Breeding - Cindy Petshop')]
class Breedings extends Component
{
    public function render()
    {
        $breedings = Breeding::with(['breedingPackage', 'categoryAnimals', 'breeds'])
            ->where('is_active', true)
            ->get();

        return view('livewire.pages.breeding', [
            'breedings' => $breedings
        ]);
    }
}
