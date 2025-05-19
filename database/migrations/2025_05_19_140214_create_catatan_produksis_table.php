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
            $table->string('sku_product');
            $table->string('nama_product');
            $table->string('packaging');
            $table->integer('quantity');
            $table->json('sku_induk');
            $table->json('gramasi');
            $table->string('total_terpakai');
            // gramasi * quantity
            $table->timestamps();

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
