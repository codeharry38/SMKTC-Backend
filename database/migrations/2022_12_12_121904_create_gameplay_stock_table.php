<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameplaysStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gameplay_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('gameplay');
            $table->unsignedInteger('stock');
            $table->unsignedInteger('ticket');
            $table->string('assign_value')->defalut(20000);
            $table->string('low')->defalut(0);
            $table->string('high')->defalut(0);
            $table->string('open')->defalut(0);
            $table->string('close')->defalut(0);
            $table->string('volume')->defalut(0);
            $table->string('shares')->defalut(0);
            $table->string('share_rate')->defalut(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gameplay_stocks');
    }
}
