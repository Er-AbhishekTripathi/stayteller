<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBravoRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bravo_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('property_id')->nullable();
            $table->string('name',255)->nullable();
            $table->string('room_info',255)->nullable();
            $table->string('amenities_details',255)->nullable();
            $table->string('no_of_room',255)->nullable();
            $table->string('price_per_month',255)->nullable();
            $table->string('deposite',255)->nullable();
            $table->tinyInteger('refundable')->nullable();
            $table->bigInteger('create_user')->nullable();
            $table->bigInteger('update_user')->nullable();
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
        Schema::dropIfExists('bravo_rooms');
    }
}
