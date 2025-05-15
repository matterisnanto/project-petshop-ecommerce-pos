<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Breeding - Cindy Petshop')]
class Breeding extends Component
{
    public function render()
    {
        return view('livewire.pages.breeding');
    }
}
