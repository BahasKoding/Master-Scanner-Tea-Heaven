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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable(false);
            $table->string('action')->nullable(false);
            $table->bigInteger('action_id')->nullable(true);
            $table->text('note')->nullable(true);
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->timestamps();

            // Simple indexes untuk performa
            $table->index(['category', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
