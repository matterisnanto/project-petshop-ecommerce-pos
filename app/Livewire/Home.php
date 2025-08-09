<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

#[Title('Home - CindyPetshop')]
class Home extends Component
{

    public function render()
    {
        return view('livewire.pages.home');
    }
}
