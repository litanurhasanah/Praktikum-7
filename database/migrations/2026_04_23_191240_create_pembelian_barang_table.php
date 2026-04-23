<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Kita tambahkan semua kolom yang kurang di sini
            if (!Schema::hasColumn('barang', 'kode_barang')) {
                $table->string('kode_barang')->after('id')->unique()->nullable();
            }
            if (!Schema::hasColumn('barang', 'harga_barang')) {
                $table->decimal('harga_barang', 15, 2)->after('nama_barang')->default(0);
            }
            if (!Schema::hasColumn('barang', 'stok')) {
                $table->integer('stok')->after('harga_barang')->default(0);
            }
            if (!Schema::hasColumn('barang', 'rating')) {
                $table->float('rating')->after('stok')->default(0);
            }
            if (!Schema::hasColumn('barang', 'foto')) {
                $table->string('foto')->after('rating')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['kode_barang', 'harga_barang', 'stok', 'rating', 'foto']);
        });
    }
};