<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFrom151To160 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('paid', 'bravo_bookings')) {
                $table->decimal('paid',10,2)->nullable();
            }
        });

        Schema::table('bravo_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('pay_now', 'bravo_bookings')) {
                $table->decimal('pay_now',10,2)->nullable();
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
