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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->boolean('side')->default(false);
            $table->string('symbol');
            $table->unsignedBigInteger('user_id');
            $table->decimal('price', 19, 8)->default(0);
            $table->decimal('amount', 19, 8)->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index(['symbol', 'status', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
