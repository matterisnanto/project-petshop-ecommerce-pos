<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Pet Grooming - Cindy Petshop')]
class PetGrooming extends Component
{
    public function render()
    {
        return view('livewire.pages.pet-grooming');
    }
}
