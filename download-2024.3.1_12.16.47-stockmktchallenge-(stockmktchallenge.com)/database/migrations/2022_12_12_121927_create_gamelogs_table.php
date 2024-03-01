<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gamelogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('game_play');
            $table->unsignedInteger('game');
            $table->unsignedInteger('player');
            $table->unsignedInteger('ticket');
            $table->unsignedInteger('stock_one');
            $table->unsignedInteger('stock_two');
            $table->unsignedInteger('stock_three');
            $table->unsignedInteger('stock_four');
            $table->unsignedInteger('stock_five');
            $table->unsignedInteger('replace_stock');
            $table->unsignedInteger('selected_stock');
            $table->unsignedInteger('prize');
            $table->string('stock_one_value');
            $table->string('stock_two_value');
            $table->string('stock_three_value');
            $table->string('stock_four_value');
            $table->string('stock_five_value');
            $table->string('action_type');
            $table->dateTime('action_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gamelogs');
    }
}
