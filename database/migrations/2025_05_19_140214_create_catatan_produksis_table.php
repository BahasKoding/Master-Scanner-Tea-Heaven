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
        Schema::create('catatan_produksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('packaging');
            $table->integer('quantity');

            // JSON array of bahan_baku IDs
            $table->json('sku_induk')->comment('Stores array of bahan_baku IDs');

            // JSON array of corresponding gramasi values
            $table->json('gramasi')->comment('Stores array of gramasi values corresponding to each bahan_baku');

            // JSON array of corresponding total_terpakai values
            $table->json('total_terpakai')->comment('Stores array of total_terpakai values corresponding to each bahan_baku');

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_produksis');
    }
};
