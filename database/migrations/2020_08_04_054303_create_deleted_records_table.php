<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_records', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->text('data')->nullable();
            $table->text('ip')->nullable();
            $table->bigInteger('deleted_by')->unsigned();
            $table->foreign('deleted_by')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::drop('deleted_records');
    }
}
