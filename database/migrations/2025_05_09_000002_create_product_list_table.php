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
        Schema::create('product_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('sku')->unique();
            $table->string('pack');
            $table->string('product_name');
            $table->unsignedBigInteger('supplier_id');
            $table->string('gramasi');
            $table->timestamps();

            // Add foreign key to category_products table
            $table->foreign('category_id')
                ->references('id')
                ->on('category_products')
                ->onDelete('restrict');

            // Add foreign key to suppliers table
            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_list');
    }
};
