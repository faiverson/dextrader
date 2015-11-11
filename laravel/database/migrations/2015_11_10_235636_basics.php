<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Basics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table)
		{
			$table->integer('id');
			$table->string('role')->unique();
			$table->timestamps();
			$table->primary('id');
		});

		Schema::create('password_resets', function(Blueprint $table)
		{
			$table->string('email')->index();
			$table->string('token')->index();
			$table->timestamp('created_at');
		});

		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('first_name', 50); //->nullable()
			$table->string('last_name', 50);
			$table->string('username', 50);
			$table->string('email')->unique('email');
			$table->tinyInteger('active')->default(1);
			$table->string('password', 200);
			$table->integer('role_id', false)->default(1);
			$table->rememberToken();
			$table->softDeletes();
			$table->timestamps();
			$table->foreign('role_id')->references('id')->on('roles');
		});
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
	public function down()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('password_resets');
		Schema::dropIfExists('roles');
		Schema::dropIfExists('users');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
