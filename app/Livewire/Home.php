<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Home extends Component
{
    // public function mount()
    // {
    //     Session::forget('cart_totals');
    // }
    public function render()
    {
        return view('livewire.pages.home');
    }
}
