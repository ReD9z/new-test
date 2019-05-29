<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('city_id')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('actual_title')->nullable();
            $table->string('legal_address')->nullable();
            $table->string('actual_address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bik')->nullable();
            $table->string('cor_score')->nullable();
            $table->string('settlement_account')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
