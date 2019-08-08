<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('orders_id')->unsigned()->nullable();
            $table->foreign('orders_id')->references('id')->on('orders')->onDelete('cascade');
            $table->bigInteger('installer_id')->unsigned()->nullable();
            $table->foreign('installer_id')->references('id')->on('installers')->onDelete('cascade');
            $table->bigInteger('types_to_works_id')->unsigned()->nullable();
            $table->foreign('types_to_works_id')->references('id')->on('types_to_works')->onDelete('cascade');
            $table->bigInteger('status')->nullable();
            $table->datetime('task_date_completion')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
