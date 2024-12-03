<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use Illuminate\Http\Request;

use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class TransaksiDetailController extends Controller
{
    public function index()
    {
        $transaksidetail = TransaksiDetail::with('transaksi')->orderBy('id','DESC')->get();

        return view('transaksidetail.index', compact('transaksidetail') );
    }

    public function detail(Request $request)
    {
        $transaksi = Transaksi::with('transaksidetail')->findOrFail($request->id_transaksi);

        return view('transaksidetail.detail',compact('transaksi') );
    }

    public function edit($id)
    {
        $transaksidetail = TransaksiDetail::findOrFail($id);
        return view('transaksidetail.edit', compact('transaksidetail') );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|numeric',
        ]);

        // Gunakan transaction
        DB::beginTransaction();
        try {
            $transaksidetail = TransaksiDetail::findOrFail($id);
            $transaksi = Transaksi::findOrFail($transaksidetail->id_transaksi);

            $transaksidetail->nama_produk = $request->input('nama_produk');
            $transaksidetail->harga_satuan = $request->input('harga_satuan');
            $transaksidetail->jumlah = $request->input('jumlah');
            $transaksidetail->subtotal = $request->input('harga_satuan') * $request->input('jumlah');
            $transaksidetail->save();

            $total_harga = $transaksi->transaksidetail->sum('subtotal');
            $transaksi->total_harga = $total_harga;
            $transaksi->kembalian = $transaksi->bayar - $total_harga;
            $transaksi->save();

            DB::commit();

            return redirect('transaksidetail/'.$transaksidetail->id_transaksi)->with('pesan', 'Berhasil mengubah data');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Transaction' => 'Gagal menambahkan data'])->withInput();
        }
    }

    public function destroy($id)
    {

        DB::beginTransaction();
        try{
            $transaksidetail = TransaksiDetail::findOrFail($id);
            $transaksi = Transaksi::findOrFail($transaksidetail->id_transaksi);
            $transaksidetail->delete();

            $total_harga = $transaksi->transaksidetail->sum('subtotal');
            $transaksi->total_harga = $total_harga;
            $transaksi->kembalian = $transaksi->bayar - $total_harga;
            $transaksi->save();

            DB::commit();

            return redirect('transaksidetail/'.$transaksidetail->id_transaksi)->with('pesan', 'Berhasil menghapus data');
        } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Transaction' => 'Gagal menghapus data'])->withInput();
        }
    }
}
