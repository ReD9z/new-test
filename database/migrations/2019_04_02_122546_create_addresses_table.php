<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('city_id')->nullable()->unsigned();
            $table->foreign('city_id')->references('id')->on('cities_to_works');
            $table->string('area')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('number_entrances')->nullable();
            $table->string('management_company')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
