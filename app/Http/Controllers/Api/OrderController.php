<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    // GET /orders
    public function index()
    {
        $orders = Order::with(['product', 'posTransaction.paymentMethod'])->get();

        $orders->transform(function ($detail_order) {
            return [
                'id' => $detail_order->id,
                'transaction_name' => optional($detail_order->posTransaction)->name ?? '-',
                'payment_method' => [
                    'name' => optional(optional($detail_order->posTransaction)->paymentMethod)->name ?? 'Unknown',
                    'is_cash' => optional(optional($detail_order->posTransaction)->paymentMethod)->is_cash ?? false,
                ],
                'product' => [
                    'product_id' => optional($detail_order->product)->id,
                    'product_name' => optional($detail_order->product)->name ?? '-',
                ],
                'quantity' => $detail_order->quantity ?? 0,
                'unit_price' => $detail_order->unit_price ?? 0,
                'created_at' => $detail_order->created_at,
                'updated_at' => $detail_order->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    // POST /orders
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pos_transaction_id' => 'required|exists:pos_transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
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
            $product = Product::lockForUpdate()->find($request->product_id);

            if (!$product || $product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk produk: ' . ($product->name ?? 'Produk tidak ditemukan'),
                ], 422);
            }

            $detail_order = Order::create([
                'pos_transaction_id' => $request->pos_transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
            ]);

            $product->decrement('stock', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $detail_order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /orders/{id}
    public function show($id)
    {
        $detail_order = Order::with(['product', 'posTransaction.paymentMethod'])->find($id);

        if (!$detail_order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $detail_order
        ], 200);
    }

    // PUT /orders/{id}
    public function update(Request $request, $id)
    {
        $detail_order = Order::find($id);

        if (!$detail_order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $detail_order->update($request->only(['quantity', 'unit_price']));

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diperbarui',
            'data' => $detail_order
        ], 200);
    }

    // DELETE /orders/{id}
    public function destroy($id)
    {
        $detail_order = Order::find($id);

        if (!$detail_order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $detail_order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus'
        ], 200);
    }
}
