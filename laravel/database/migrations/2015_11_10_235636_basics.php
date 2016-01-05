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
		Schema::create('password_resets', function(Blueprint $table)
		{
			$table->string('email')->index();
			$table->string('token')->index();
			$table->timestamp('created_at');
		});

		Schema::create('users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('first_name', 50)->nullable(false);
			$table->string('last_name', 50)->nullable(false);
			$table->string('username', 50)->nullable(false)->unique('username')->index();
			$table->string('email')->nullable(false)->unique('email');
			$table->string('phone', 15);
			$table->tinyInteger('active')->nullable(false)->default(1);
			$table->string('password', 200)->nullable(false);
			$table->string('ip_address', 25)->nullable();
			$table->bigInteger('enroller_id')->unsigned()->nullable();
			$table->foreign('enroller_id')
				->references('id')->on('users');
			$table->string('commissions', 4)->nullable(false)->default(40);
			$table->string('parent_commissions', 4)->nullable(false)->default(10);
			$table->rememberToken();
			$table->softDeletes();
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
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('password_resets');
		Schema::dropIfExists('users');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
