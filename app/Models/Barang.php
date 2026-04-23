<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $guarded = [];

    /**
     * Logika untuk pembuatan Kode Barang otomatis (BRG-001, dst)
     */
    public static function getKodeBarang()
    {
        // Mencari kode barang terakhir
        $lastBarang = self::orderBy('id', 'desc')->first();

        if (!$lastBarang) {
            return 'BRG-001';
        }

        // Mengambil angka dari kode terakhir (misal BRG-001 menjadi 001)
        $lastNumber = (int) substr($lastBarang->kode_barang, 4);
        $nextNumber = $lastNumber + 1;

        // Menggabungkan kembali menjadi format BRG-00X
        return 'BRG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke detail pembelian
     */
    public function pembelianBarang(): HasMany
    {
        return $this->hasMany(PembelianBarang::class, 'barang_id');
    }

    /**
     * Relasi ke detail penjualan (Jika ada di Modul 7)
     */
    public function penjualanBarang(): HasMany
    {
        return $this->hasMany(PenjualanBarang::class, 'barang_id');
    }
}