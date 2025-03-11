<?php

namespace App\Http\Controllers;

use App\Models\OlshopTransaction;
use Illuminate\Http\Request;

class TrxController extends Controller
{
    public function index()
    {
        return view('pages.trxcheck');
    }

    public function searchTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        $transactionId = $request->input('transaction_id');

        // Cari transaksi berdasarkan ID
        $transaction = OlshopTransaction::where('trx_id', $transactionId)->first();

        if ($transaction) {
            // Jika transaksi ditemukan, tampilkan detail
            return redirect()->route('trx')->with('transaction', $transaction);
        } else {
            // Jika tidak ditemukan, kembalikan pesan error
            return redirect()->route('trx')->with('error', 'Transaction ID not found.');
        }

        return redirect()->route('trx');
    }
}
