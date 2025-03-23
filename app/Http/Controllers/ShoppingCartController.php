<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $quantity = $request->input('quantity', 1);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $cart = session()->get('cart', []);

        // Hitung total quantity yang akan ditambahkan ke keranjang
        $totalQuantityInCart = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $totalQuantityRequested = $totalQuantityInCart + $quantity;

        // Cek apakah stok mencukupi
        if ($product->stock < $totalQuantityRequested) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Sisa stok hanya ' . $product->stock,
            ], 400); // 400 Bad Request
        }

        // Tambahkan produk ke keranjang
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "slug" => $product->slug,
                "barcode" => $product->barcode,
                "stock" => $product->stock,
                "quantity" => $quantity,
                "price" => $product->selling_price,
                "thumbnail" => $product->thumbnail ? 'storage/' . $product->thumbnail : 'https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg',
            ];
        }

        session()->put('cart', $cart);

        $total = $this->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'product' => $cart[$id],
            'total' => $total,
            'cart' => $cart,
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

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
            // Hitung subtotal untuk item tertentu
            $total = $this->calculateTotal($cart);

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

    private function calculateSubTotal($cart)
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        return view('pages.checkout', compact('cart', 'total'));
    }
}
