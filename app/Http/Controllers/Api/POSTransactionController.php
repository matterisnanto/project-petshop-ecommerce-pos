<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PosTransactionController extends Controller
{
    // GET /postransactions → postransactions.index
    public function index()
    {
        $transactions = PosTransaction::with(['order.product', 'paymentMethod'])->get();

        $transactions->transform(function ($transaction) {
            return [
                'id' => $transaction->id,
                'name' => $transaction->name,
                'email' => $transaction->email,
                'gender' => $transaction->gender,
                'total_price' => $transaction->total_price ?? 0,
                'note' => $transaction->note ?? '',
                'paid_amount' => $transaction->paid_amount ?? 0,
                'change_amount' => $transaction->change_amount ?? 0,
                'payment_method' => [
                    'name' => optional($transaction->paymentMethod)->name ?? 'Unknown',
                    'is_cash' => optional($transaction->paymentMethod)->is_cash ?? false,
                ],
                'items' => $transaction->order->map(function ($order) {
                    return [
                        'product_id' => optional($order->product)->id,
                        'product_name' => optional($order->product)->name ?? '-',
                        'quantity' => $order->quantity,
                        'unit_price' => $order->unit_price ?? 0 ,
                    ];
                }),
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transactions
        ], 200);
    }

    // POST /postransactions → postransactions.store
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
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
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk produk: ' . ($product->name ?? 'Produk tidak ditemukan')
                    ], 422);
                }
            }

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

            foreach ($request->items as $item) {
                $transaction->order()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);

                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'data' => $transaction
            ], 201);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // GET /postransactions/{id} → postransactions.show
    public function show($id)
    {
        $transaction = PosTransaction::with(['order.product', 'paymentMethod'])->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ], 200);
    }

    // PUT /postransactions/{id} → postransactions.update
    public function update(Request $request, $id)
    {
        $transaction = PosTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'email' => 'nullable|string',
            'gender' => 'nullable|string',
            'total_price' => 'sometimes|required|numeric',
            'note' => 'nullable|string',
            'payment_method_id' => 'sometimes|required|exists:payment_methods,id',
            'paid_amount' => 'sometimes|required|numeric',
            'change_amount' => 'sometimes|required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction->update($request->only([
            'name', 'email', 'gender', 'total_price', 'note', 'payment_method_id', 'paid_amount', 'change_amount'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaction
        ], 200);
    }

    // DELETE /postransactions/{id} → postransactions.destroy
    public function destroy($id)
    {
        $transaction = PosTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ], 200);
    }
}