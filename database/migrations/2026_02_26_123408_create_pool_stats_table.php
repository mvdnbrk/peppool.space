<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_stats', function (Blueprint $table) {
            $table->id();
            $table->timestamp('hashrate_timestamp')->index();
            $table->double('avg_hashrate')->unsigned();
            $table->unsignedSmallInteger('pool_id');
            $table->float('share');
            $table->enum('type', ['daily', 'weekly'])->default('daily')->index();
            $table->timestamps();

            $table->foreign('pool_id')->references('id')->on('pools')->onDelete('cascade');
            $table->unique(['hashrate_timestamp', 'pool_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_stats');
    }
};
