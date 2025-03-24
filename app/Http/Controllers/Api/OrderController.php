<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['product', 'posTransaction.paymentMethod'])->get();

        $orders->transform(function ($order) {
            return [
                'id' => $order->id,
                'postransaction' => optional($order->posTransaction)->name ?? '-',
                'payment_method' => [
                    'name' => optional(optional($order->posTransaction)->paymentMethod)->name ?? 'Unknown',
                    'is_cash' => optional(optional($order->posTransaction)->paymentMethod)->is_cash ?? false,
                ],
                'product' => [
                    'product_id' => optional($order->product)->id,
                    'product_name' => optional($order->product)->name ?? '-',
                    'quantity' => $order->quantity ?? 0,
                    'unit_price' => $order->unit_price ?? 0
                ]
            ];
        });

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'pos_transaction_id' => 'required|exists:pos_transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Ambil produk
            $product = Product::lockForUpdate()->find($request->product_id);

            if (!$product || $product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi'
                ], 422);
            }

            // Simpan order
            $order = Order::create([
                'pos_transaction_id' => $request->pos_transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price, // Gunakan harga dari database
            ]);

            // Kurangi stok produk
            $product->decrement('stock', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, coba lagi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}