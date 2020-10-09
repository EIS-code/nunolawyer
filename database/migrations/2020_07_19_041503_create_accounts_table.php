<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('clients')->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
			$table->timestamp('date');
            $table->float('received_amount');
            $table->bigInteger('purpose_article_id')->unsigned();
            $table->foreign('purpose_article_id')->references('id')->on('purpose_articles')->onDelete('cascade');
			$table->enum('is_removed', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
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
        Schema::drop('accounts');
    }
}
