<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images_to_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('address_to_orders_id')->unsigned()->nullable();
            $table->foreign('address_to_orders_id')->references('id')->on('address_to_orders')->onDelete('cascade');
            $table->bigInteger('files_id')->unsigned()->nullable();
            $table->foreign('files_id')->references('id')->on('files')->onDelete('cascade');
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
        Schema::dropIfExists('images_to_orders');
    }
}
