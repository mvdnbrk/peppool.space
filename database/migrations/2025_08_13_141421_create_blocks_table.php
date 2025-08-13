<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->unsignedInteger('height')->primary();
            $table->string('hash', 64)->unique();
            $table->integer('tx_count');
            $table->unsignedInteger('size');
            $table->decimal('difficulty', 20, 8);
            $table->unsignedInteger('nonce');
            $table->integer('version');
            $table->string('merkleroot', 64);
            $table->string('chainwork', 64);
            $table->json('auxpow')->nullable();
            $table->timestamp('created_at');

            $table->index('hash');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}
