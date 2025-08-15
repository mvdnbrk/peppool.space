<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('tx_id', 64)->primary();
            $table->unsignedInteger('block_height')->index();
            $table->unsignedInteger('size');
            $table->decimal('fee', 16, 8)->default(0);
            $table->unsignedTinyInteger('version');
            $table->unsignedInteger('locktime')->default(0);
            $table->boolean('is_coinbase')->default(false)->index();

            $table->foreign('block_height')->references('height')->on('blocks')->onDelete('cascade');

            $table->index(['block_height', 'is_coinbase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
