<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrizeMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('prize_metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('prize');
            $table->string('position');
            $table->string('position_type');
            $table->string('prize_type');
            $table->string('prize_value');
            $table->softDeletes();
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
       Schema::dropIfExists('prize_metas');
    }
}
