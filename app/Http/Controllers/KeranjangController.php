<?php

namespace App\Http\Controllers;

use App\Models\ItemKeranjang;
use App\Models\Produk;
use App\Models\VarianProduk;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    private function cartAttributes(): array
    {
        return array_merge(['session_id' => session()->getId()], auth()->check() ? ['user_id' => auth()->id()] : []);
    }

    // Tampilkan isi keranjang
    public function index(Request $request)
    {
        $items = ItemKeranjang::with(['produk.gambarUtama', 'varian.gambarVarianUtama'])
            ->where($this->identifier())
            ->get();

        $subtotal = $items->sum(function ($item) {
            $harga = $item->produk->harga + ($item->varian->penyesuaian_harga ?? 0);
            return $harga * $item->jumlah;
        });

        return response()->json([
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'produk_id' => $item->produk_id,
                    'nama' => $item->produk->nama,
                    'gambar' => $item->varian?->gambarVarianUtama?->full_url ?? $item->produk->gambarUtama?->full_url,
                    'ukuran' => $item->varian?->ukuran,
                    'warna' => $item->varian?->warna,
                    'kode_warna' => $item->varian?->kode_warna,
                    'harga' => $item->produk->harga + ($item->varian?->penyesuaian_harga ?? 0),
                    'jumlah' => $item->jumlah,
                    'subtotal' => ($item->produk->harga + ($item->varian?->penyesuaian_harga ?? 0)) * $item->jumlah,
                ];
            }),
            'total_item' => $items->sum('jumlah'),
            'subtotal' => $subtotal,
        ]);
    }

    // Tambah item ke keranjang
    public function tambah(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'varian_id' => 'nullable|exists:varian_produks,id',
            'jumlah' => 'integer|min:1|max:99',
        ]);

        $identifier = $this->identifier();
        $jumlah = $request->input('jumlah', 1);

        // Cek apakah item sudah ada di keranjang
        $existing = ItemKeranjang::where($identifier)
            ->where('produk_id', $request->produk_id)
            ->where('varian_id', $request->varian_id)
            ->first();

        // Cek stok terhadap total item yang akan ada di keranjang
        if ($request->varian_id) {
            $varian = VarianProduk::find($request->varian_id);

            if (! $varian || $varian->produk_id !== (int) $request->produk_id) {
                return response()->json(['error' => 'Varian produk tidak valid'], 422);
            }

            $requestedTotal = ($existing?->jumlah ?? 0) + $jumlah;

            if ($varian->stok < $requestedTotal) {
                return response()->json(['error' => 'Stok tidak mencukupi'], 422);
            }
        }

        if ($existing) {
            $existing->jumlah += $jumlah;
            $existing->save();
        } else {
            ItemKeranjang::create(array_merge($this->cartAttributes(), [
                'produk_id' => $request->produk_id,
                'varian_id' => $request->varian_id,
                'jumlah' => $jumlah,
            ]));
        }

        $totalItem = ItemKeranjang::where($identifier)->sum('jumlah');

        return response()->json([
            'success' => true,
            'pesan' => 'Berhasil ditambahkan ke keranjang',
            'total_item' => $totalItem,
        ]);
    }

    // Update jumlah item
    public function update(Request $request, int $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:99',
        ]);

        $item = ItemKeranjang::where($this->identifier())
            ->where('id', $id)
            ->firstOrFail();

        // Cek stok
        if ($item->varian && $item->varian->stok < $request->jumlah) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 422);
        }

        $item->jumlah = $request->jumlah;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Hapus item dari keranjang
    public function hapus(int $id)
    {
        ItemKeranjang::where($this->identifier())
            ->where('id', $id)
            ->delete();

        $totalItem = ItemKeranjang::where($this->identifier())->sum('jumlah');

        return response()->json([
            'success' => true,
            'total_item' => $totalItem,
        ]);
    }

    // Hitung total item di keranjang (untuk badge)
    public function jumlah()
    {
        $total = ItemKeranjang::where($this->identifier())->sum('jumlah');

        return response()->json(['total_item' => $total]);
    }
}
