<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEntrancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->bigInteger('file_id')->unsigned()->nullable();
            $table->foreign('file_id')->references('id')->on('files')->onDelete(DB::raw('set null'));
            $table->bigInteger('address_to_orders_id')->unsigned()->nullable();
            $table->foreign('address_to_orders_id')->references('id')->on('address_to_orders')->onDelete('cascade');
            $table->string('number')->nullable();
            $table->boolean('shield')->default(1);
            $table->boolean('glass')->default(1);
            $table->boolean('information')->default(1);
            $table->boolean('mood')->default(1);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('entrances');
    }
}
