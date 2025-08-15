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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bahan_baku', 'finished_goods'])->comment('Jenis opname: bahan_baku, finished_goods');
            $table->date('tanggal_opname')->comment('Tanggal pelaksanaan stock opname');
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft')->comment('Status opname');
            $table->unsignedBigInteger('created_by')->comment('User yang membuat opname');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Index untuk performance
            $table->index(['type', 'tanggal_opname']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
