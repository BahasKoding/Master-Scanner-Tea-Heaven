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
            $table->foreignId('id_product');
            $table->integer('stok_awal');
            $table->integer('stok_masuk'); // dari catatan produksi
            $table->integer('stok_keluar'); // dari hasil scanner
            $table->integer('defective');
            $table->integer('live_stock');
            $table->timestamps();

            $table->foreign('id_product')->references('id')->on('products');
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
