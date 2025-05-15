<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Pet Hotel - Cindy Petshop')]
class PetHotel extends Component
{
    public function render()
    {
        return view('livewire.pages.pet-hotel');
    }
}
