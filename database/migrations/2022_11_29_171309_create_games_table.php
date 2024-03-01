<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('entrance_deadline');
            $table->string('cost');
            $table->string('sponsor');
            $table->string('online_tickets');
            $table->string('offline_tickets');
            $table->string('free_tickets');
            $table->string('stripe_product');
            $table->string('stripe_product_price');
            $table->unsignedInteger('organization');
            $table->unsignedInteger('prize');
            $table->unsignedInteger('offer');
            $table->boolean('is_promoted')->default('0');
            $table->boolean('is_daily_prize')->default('0');
            $table->boolean('status');
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
        Schema::dropIfExists('games');
    }
}
