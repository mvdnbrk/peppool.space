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
        Schema::dropIfExists('address_balances');
        Schema::dropIfExists('transaction_inputs');
        Schema::dropIfExists('transaction_outputs');
        Schema::dropIfExists('transactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation as these tables are no longer part of the application logic.
    }
};
