<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gameplays', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('game');
            $table->unsignedInteger('player');
            $table->unsignedInteger('ticket');
            $table->unsignedInteger('prize');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('end_at');
            $table->dateTime('entrance_deadline');
            $table->boolean('status')->defalut(0);
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
        Schema::dropIfExists('gameplays');
    }
}
