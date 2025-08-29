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
            $table->integer('stok_awal')->default(0); 
            $table->integer('stok_masuk'); // dari catatan produksi + purchase finished goods
            $table->integer('stok_keluar'); // dari hasil scanner
            $table->integer('defective');
            $table->integer('stok_sisa'); // dari stock opname bulan kemaren
            $table->integer('live_stock');
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
