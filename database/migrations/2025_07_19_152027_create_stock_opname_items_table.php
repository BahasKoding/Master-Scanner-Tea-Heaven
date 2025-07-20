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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opname_id')->comment('ID stock opname');
            $table->unsignedBigInteger('item_id')->comment('ID item (bahan_baku/product/sticker)');
            $table->string('item_name')->comment('Nama item untuk referensi');
            $table->string('item_sku')->nullable()->comment('SKU item untuk referensi (bahan_baku: sku_induk, product: sku)');
            $table->decimal('stok_sistem', 10, 2)->default(0)->comment('Stok menurut sistem');
            $table->decimal('stok_fisik', 10, 2)->nullable()->comment('Stok hasil perhitungan fisik');
            $table->decimal('selisih', 10, 2)->default(0)->comment('Selisih (stok_fisik - stok_sistem)');
            $table->string('satuan', 20)->comment('Satuan (kg, pcs, dll)');
            $table->text('notes')->nullable()->comment('Catatan untuk item ini');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('opname_id')->references('id')->on('stock_opnames')->onDelete('cascade');
            
            // Index untuk performance
            $table->index('opname_id');
            $table->index(['item_id', 'opname_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
