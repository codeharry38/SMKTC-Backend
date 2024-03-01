<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('access_code');
            $table->string('ticket_number')->unique();
            $table->string('ticket_type')->default('not_type');
            $table->string('cost');
            $table->string('sponsor');
            $table->unsignedInteger('assign_to')->default('0');
            $table->unsignedInteger('used_by')->default('0');
            $table->unsignedInteger('organization');
            $table->unsignedInteger('game');
            $table->unsignedInteger('prize');
            $table->boolean('is_game_ative')->default('1');
            $table->boolean('is_used')->default('0');
            $table->boolean('is_paid')->default('0');
            $table->boolean('is_promoted')->default('0');
            $table->boolean('is_daily_prize')->default('0');
            $table->timestamps();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->dateTime('entrance_deadline');
            $table->text('assign_symbol')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
