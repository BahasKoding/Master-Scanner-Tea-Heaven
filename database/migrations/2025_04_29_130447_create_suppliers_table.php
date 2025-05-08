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
        // Ensure the category_suppliers table exists first 
        // since the date on the migration file might be misleading
        if (!Schema::hasTable('category_suppliers')) {
            Schema::create('category_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        // Then create suppliers table with foreign key reference
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_supplier_id');
            $table->string('code');
            $table->string('product_name');
            $table->string('unit');
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('category_supplier_id')
                ->references('id')
                ->on('category_suppliers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop suppliers table first (with its foreign key)
        Schema::dropIfExists('suppliers');

        // Don't drop category_suppliers here since it might be managed by its own migration
    }
};
