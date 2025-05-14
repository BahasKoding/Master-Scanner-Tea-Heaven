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

        // Create new category_products table with foreign key to labels
        Schema::create('category_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_products');

        // Recreate original category_products table
        Schema::create('category_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->timestamps();
        });
    }
};
