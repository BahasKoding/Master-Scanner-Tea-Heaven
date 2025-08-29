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
        Schema::create('inventory_bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0); // dari purchase bahan baku 
            $table->integer('terpakai')->default(0); // dari catatan produksi
            $table->integer('defect')->default(0);
            $table->integer('live_stok_gudang')->default(0);
            $table->string('satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_bahan_bakus');
    }
};
