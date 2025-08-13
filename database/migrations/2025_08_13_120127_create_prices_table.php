<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3);
            $table->decimal('price', 18, 8);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['currency', 'created_at'], 'idx_currency_created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
