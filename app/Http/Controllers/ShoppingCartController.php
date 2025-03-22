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

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "slug" => $product->slug,
                "barcode" => $product->barcode,
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

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = $this->calculateTotal($cart);

        return view('pages.checkout', compact('cart', 'total'));
    }
}
