<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id('room_type_id');
            $table->string('room_type_name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->integer('room_type_id');
            $table->string('room_name');
            $table->string('description');
            $table->integer('room_rate');
            $table->integer('max_adult');
            $table->integer('max_child');
            $table->integer('adult_extra_rate');
            $table->integer('child_extra_rate');
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
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('rooms');
    }
}
