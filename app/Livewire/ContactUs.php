<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Session;

#[Title('Contact Us - CindyPetshop')]
class ContactUs extends Component
{
    // public function mount()
    // {
    //     Session::forget('cart_totals');
    // }
    public function render()
    {
        return view('livewire.pages.contact-us');
    }
}
