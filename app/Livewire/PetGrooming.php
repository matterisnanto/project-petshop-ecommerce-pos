<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grooming;
use Livewire\Attributes\Title;

#[Title('Pet Grooming - CindyPetshop')]
class PetGrooming extends Component
{
    public function render()
    {
        $groomings = Grooming::with(['groomingPackage', 'categoryAnimals', 'categoryGrooming'])
            ->where('is_active', true)
            ->get();

        return view('livewire.pages.pet-grooming', [
            'groomings' => $groomings
        ]);
    }
}
