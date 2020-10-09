<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPurposeArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_purpose_articles', function (Blueprint $table) {
            $table->id();
            $table->enum('is_removed', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
			$table->bigInteger('purpose_article_id')->unsigned();
            $table->foreign('purpose_article_id')->references('id')->on('purpose_articles')->onDelete('cascade');
			$table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->enum('is_last_inserted', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
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
        Schema::drop('client_purpose_articles');
    }
}
