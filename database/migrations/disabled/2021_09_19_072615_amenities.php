<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Amenities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amenities_category', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->id('amenities_id');
            $table->integer('category_id');
            $table->string('amenities_name');
            $table->string('description');
            $table->text('features');
            $table->text('image_ids');
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
        Schema::dropIfExists('amenities_category');
        Schema::dropIfExists('amenities');
    }
}
