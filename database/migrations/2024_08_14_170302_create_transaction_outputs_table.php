<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('tx_id', 64);
            $table->unsignedSmallInteger('output_index');
            $table->string('address', 35)->nullable()->index();
            $table->decimal('amount', 30, 8);
            $table->text('script_pub_key');
            $table->string('script_type', 20)->nullable();
            $table->text('op_return_data')->nullable();
            $table->text('op_return_decoded')->nullable();
            $table->string('op_return_protocol')->nullable()->index();
            $table->boolean('is_spent')->default(false)->index();
            $table->string('spent_by_tx_id', 64)->nullable()->index();
            $table->unsignedInteger('spent_by_input_index')->nullable();

            $table->foreign('tx_id')
                ->references('tx_id')
                ->on('transactions')
                ->onDelete('cascade');

            $table->index(['address', 'is_spent']);
            $table->index(['address', 'amount']);
            $table->unique(['tx_id', 'output_index']);
            $table->index(['spent_by_tx_id', 'spent_by_input_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_outputs');
    }
};
