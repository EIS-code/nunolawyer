<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoaAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poa_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('text');
			$table->string('file')->nullable();
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
        Schema::drop('poa_agreements');
    }
}
