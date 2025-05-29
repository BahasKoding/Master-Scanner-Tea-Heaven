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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->integer('qty_pembelian');
            $table->date('tanggal_kedatangan_barang')->nullable();
            $table->integer('qty_barang_masuk')->default(0);
            $table->integer('barang_defect_tanpa_retur')->default(0);
            $table->integer('barang_diretur_ke_supplier')->default(0);
            $table->integer('total_stok_masuk')->default(0);
            $table->string('checker_penerima_barang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
