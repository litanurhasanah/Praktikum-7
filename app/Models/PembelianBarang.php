<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barang';
    protected $guarded = [];

    protected static function booted()
    {
        // 1. SAAT DATA BARU MASUK
        static::created(function ($item) {
            // Update Stok Barang (Nambah)
            $item->barang->increment('stok', $item->jumlah);

            // Hitung Subtotal baris ini
            $subtotal = $item->jumlah * $item->harga_beli;
            $item->updateQuietly(['subtotal' => $subtotal]);

            // Update Total di Tabel Pembelian (Header)
            $item->updateTotalPembelian();
        });

        // 2. SAAT DATA DIHAPUS (Bisa dari tombol hapus di Edit atau Delete nota)
        static::deleting(function ($item) {
            // Balikin Stok (Kurangi) sebelum data hilang
            if ($item->barang) {
                $item->barang->decrement('stok', $item->jumlah);
            }
        });

        // 3. SETELAH DATA TERHAPUS
        static::deleted(function ($item) {
            // Update ulang Total di Tabel Pembelian
            $item->updateTotalPembelian();
        });
    }

    // Fungsi pembantu untuk update total di tabel induk
    public function updateTotalPembelian()
    {
        if ($this->pembelian) {
            $totalBaru = PembelianBarang::where('pembelian_id', $this->pembelian_id)->sum('subtotal');
            $this->pembelian->update(['total' => $totalBaru]);
        }
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}