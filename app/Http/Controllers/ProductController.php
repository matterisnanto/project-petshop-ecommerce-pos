<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Fetch all products
        $product = Product::paginate(9);

        return view('pages.product', compact('product'));
    }

    public function showAll()
    {
        $product = Product::all();
        return view('pages.product', compact('product'));
    }

    public function show($slug)
    {
        // Cari produk berdasarkan slug dan eager load relasi photos dan brand
        $product = Product::with(['photos', 'brand', 'category'])->where('slug', $slug)->firstOrFail();

        // Tampilkan view detail produk dengan data yang ditemukan
        return view('pages.productdetail', compact('product'));
    }
}
