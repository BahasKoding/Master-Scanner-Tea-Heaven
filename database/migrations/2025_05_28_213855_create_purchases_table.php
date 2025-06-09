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
            $table->enum('kategori', ['bahan_baku', 'finished_goods'])->default('bahan_baku');

            // Flexible item reference - no foreign key constraint to allow both bahan_baku and product IDs
            $table->unsignedBigInteger('bahan_baku_id'); // This will store either bahan_baku_id or product_id based on kategori

            $table->integer('qty_pembelian');
            $table->date('tanggal_kedatangan_barang')->nullable();
            $table->integer('qty_barang_masuk')->default(0);
            $table->integer('barang_defect_tanpa_retur')->default(0);
            $table->integer('barang_diretur_ke_supplier')->default(0);
            $table->integer('total_stok_masuk')->default(0);
            $table->string('checker_penerima_barang')->nullable();
            $table->timestamps();

            // Add index for better performance
            $table->index(['kategori', 'bahan_baku_id']);
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
