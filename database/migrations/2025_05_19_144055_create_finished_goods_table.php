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
        Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->unique('product_id');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0); // dari catatan produksi + purchase finished goods
            $table->integer('stok_keluar')->default(0); // dari hasil scanner
            $table->integer('defective')->default(0);
            $table->integer('stok_sisa')->default(0); // dari stock opname bulan kemaren
            $table->integer('live_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finished_goods');
    }
};
