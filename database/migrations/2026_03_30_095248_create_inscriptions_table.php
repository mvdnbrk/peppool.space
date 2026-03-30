<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('inscription_id', 80)->unique();
            $table->string('parent_id', 80)->nullable()->index();
            $table->string('delegate_id', 80)->nullable()->index();
            $table->string('content_encoding', 10)->nullable();
            $table->string('content_type', 50)->nullable()->index();
            $table->unsignedInteger('content_length')->nullable();
            $table->longText('content')->nullable();
            $table->json('properties')->nullable();
            $table->unsignedInteger('flags')->default(0)->index();
            $table->unsignedInteger('block')->index();

            $table->foreign('block')
                ->references('height')
                ->on('blocks')
                ->onDelete('cascade');
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('inscription_id')
                ->on('inscriptions')
                ->onDelete('set null');

            $table->foreign('delegate_id')
                ->references('inscription_id')
                ->on('inscriptions')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['delegate_id']);
        });

        Schema::dropIfExists('inscriptions');
    }
};
