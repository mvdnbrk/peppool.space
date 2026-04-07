<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_users', function (Blueprint $table) {
            $table->id();
            $table->string('address')->unique();
            $table->string('version', length: 12);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_users');
    }
};
