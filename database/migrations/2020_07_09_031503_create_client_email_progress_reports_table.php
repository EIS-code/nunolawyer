<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientEmailProgressReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_email_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
			$table->string('progress_report');
			$table->string('file')->nullable();
			$table->enum('is_removed', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
			$table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
        Schema::drop('client_email_progress_reports');
    }
}
