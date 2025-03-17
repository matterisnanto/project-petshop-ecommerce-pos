<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    //
    public function addToCart(Request $request, $id)
    {
        // Validasi input quantity
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity', 1);

        // Cari produk berdasarkan ID
        $product = Product::find($id);

        // Jika produk tidak ditemukan, kembalikan respons error
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // Ambil data keranjang dari session
        $cart = session()->get('cart', []);

        // Jika produk sudah ada di keranjang, tambahkan quantity
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            // Jika produk belum ada di keranjang, tambahkan produk baru
            $cart[$id] = [
                "name" => $product->name,
                "barcode" => $product->barcode,
                "quantity" => $quantity,
                "price" => $product->selling_price,
                "thumbnail" => $product->thumbnail ? 'storage/' . $product->thumbnail : 'https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg',
            ];
        }

        // Simpan keranjang ke session
        session()->put('cart', $cart);

        // Hitung total harga keranjang
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Kembalikan respons sukses dengan data produk dan total
        return response()->json([
            'success' => true,
            'product' => $cart[$id],
            'total' => $total,
            'cart' => $cart, // Sertakan seluruh data keranjang untuk keperluan frontend
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('pages.shoppingcart', compact('cart', 'total'));
    }

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->input('quantity');

            if ($cart[$id]['quantity'] <= 0) {
                unset($cart[$id]);
            }

            session()->put('cart', $cart);

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'total' => $total
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'total' => $total,
            'cart_count' => count($cart)
        ]);
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
