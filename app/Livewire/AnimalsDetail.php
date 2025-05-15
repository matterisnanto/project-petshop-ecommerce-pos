<?php

namespace App\Livewire;

use App\Models\Animals;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Animals - Cindy Petshop')]
class AnimalsDetail extends Component
{
    public Animals $animals;

    public function mount(Animals $animals)
    {
        $this->animals = $animals;
    }

    public function render()
    {
        $this->animals->load('animalsPhotos');
        return view('livewire.pages.animals-detail', [
            'animals' => $this->animals,
        ]);
    }
}
