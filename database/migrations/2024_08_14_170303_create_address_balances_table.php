<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('address_balances', function (Blueprint $table) {
            $table->id();
            $table->string('address', 35)->unique()->index();
            $table->decimal('balance', 16, 8)->default(0)->index();
            $table->decimal('total_received', 16, 8)->default(0);
            $table->decimal('total_sent', 16, 8)->default(0);
            $table->unsignedInteger('tx_count')->default(0);
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_activity')->nullable()->index();

            // Indexes for rich list and statistics
            $table->index(['balance', 'address']);
            $table->index(['total_received', 'address']);
            $table->index(['tx_count', 'address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('address_balances');
    }
};
