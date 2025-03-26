<?php

namespace App\Livewire\Navigation;

use Livewire\Component;

class BreadCrumb extends Component
{
    public $links = [];
    public $currentPage;

    public function mount(array $links, string $currentPage)
    {
        $this->links = $links;
        $this->currentPage = $currentPage;
    }

    public function render()
    {
        return view('livewire.navigation.bread-crumb');
    }
}
