<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PosTransactionController extends Controller
{
    public function index()
    {
        $transactions = PosTransaction::with('order', 'paymentMethod')->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            // Cek apakah request kosong
            if (!$request->all()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request body tidak boleh kosong'
                ], 400);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'nullable|string',
                'gender' => 'nullable|string',
                'total_price' => 'required|numeric',
                'note' => 'nullable|string',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'paid_amount' => 'required|numeric',
                'change_amount' => 'required|numeric',
                'items' => 'required|array|min:1',
                'items.*.product_id'  => 'required|exists:products,id',
                'items.*.quantity'  => 'required|integer|min:1',
                'items.*.unit_price'  => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ada kesalahan validasi',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek stok produk
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk produk: ' . ($product->name ?? 'Produk tidak ditemukan')
                    ], 422);
                }
            }

            // Buat transaksi baru
            $transaction = PosTransaction::create($request->only([
                'name',
                'email',
                'gender',
                'total_price',
                'note',
                'payment_method_id',
                'paid_amount',
                'change_amount'
            ]));

            // Simpan order terkait
            foreach ($request->items as $item) {
                $transaction->order()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);

                // Update stok produk
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}