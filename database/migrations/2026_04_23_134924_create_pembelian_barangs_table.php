<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('barang', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
    $table->foreignId('pembelian_id')->constrained('pembelian')->cascadeOnDelete();
    $table->string('nama_barang');
    $table->integer('stok')->default(0);
    $table->integer('harga_barang')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('pembelian_barang');
    }
};
