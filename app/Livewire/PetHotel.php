<?php

namespace App\Livewire;

use App\Models\Hotel;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Pet Hotel - CindyPetshop')]
class PetHotel extends Component
{
    public function render()
    {
        $hotels = Hotel::with(['hotelPackage', 'categoryAnimals'])
            ->where('is_active', true)
            ->get();

        return view('livewire.pages.pet-hotel', [
            'hotels' => $hotels
        ]);
    }
}
