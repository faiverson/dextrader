<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('credit_cards', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('name', 150)->nullable(false);
			$table->smallInteger('exp_month')->nullable(false);
			$table->smallInteger('exp_year')->nullable(false);
			$table->string('number')->nullable(false)->unique('number');
			$table->integer('first_six')->nullable(false);
			$table->integer('last_four')->nullable(false);
			$table->string('network', 15)->nullable(false);
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
		Schema::drop('credit_cards');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
