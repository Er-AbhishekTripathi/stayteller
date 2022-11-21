<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBravoContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bravo_contact', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('message')->nullable();
            $table->string('status', 50)->nullable();

            $table->string('subject', 255)->nullable();
            $table->string('phone', 15)->nullable();
            
            // $table->string('map_lat',20)->nullable();
            // $table->string('map_lng',20)->nullable();
            // $table->integer('map_zoom')->nullable();

            $table->integer('create_user')->nullable();
            $table->integer('update_user')->nullable();
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
        Schema::dropIfExists('bravo_contact');
    }
}
