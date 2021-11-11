<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookingHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_history', function (Blueprint $table) {
            $table->id('booking_history_id');
            $table->string('booking_code');
            $table->integer('room_id');
            $table->integer('account_id');
            $table->integer('no_of_adult');
            $table->integer('no_of_child');
            $table->datetime('book_start');
            $table->datetime('book_end')->nullable();
            $table->float('total_fee');
            $table->boolean('is_paid');
            $table->datetime('paid_date')->nullable();
            $table->boolean('is_reservation');
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
        Schema::drop('booking_history');
    }
}


