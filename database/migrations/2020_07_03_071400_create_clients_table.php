<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->timestamp('registration_date');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
            $table->string('secondary_email')->nullable();
            $table->date('dob')->nullable();
            $table->string('contact')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('process_address')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('work_status', [0, 1, 2])->default(0)->comment("0: Default, 1: To follow, 2: Work done all");
            $table->string('photo')->nullable();
            $table->boolean('banned')->default(false);
            // $table->timestamp('assign_date')->nullable();
            // $table->integer('assign_to')->nullable();
            $table->string('password');
            $table->string('password_2');
            $table->string('password_text');
            $table->string('password_text_2');
            $table->boolean('is_superadmin')->default(false);
			$table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_logout_at')->nullable();
            $table->enum('is_removed', ['0', '1'])->default('0')->comment('0: Nope, 1: Yes');
            $table->rememberToken();
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
