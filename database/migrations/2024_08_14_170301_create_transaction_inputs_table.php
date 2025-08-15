<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_inputs', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id', 64);
            $table->unsignedSmallInteger('input_index');
            $table->string('prev_tx_id', 64)->nullable();
            $table->unsignedInteger('prev_vout')->nullable();
            $table->string('address', 35)->nullable()->index();
            $table->decimal('amount', 30, 8)->nullable();
            $table->text('script_sig')->nullable();
            $table->unsignedBigInteger('sequence')->default(4294967295);
            $table->text('coinbase_data')->nullable();

            $table->foreign('tx_id')
                ->references('tx_id')
                ->on('transactions')
                ->onDelete('cascade');

            $table->index(['address', 'tx_id']);
            $table->index(['prev_tx_id', 'prev_vout']);
            $table->unique(['tx_id', 'input_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_inputs');
    }
};
