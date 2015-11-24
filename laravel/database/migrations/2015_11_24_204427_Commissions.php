<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Commissions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commissions', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->bigInteger('enroller_id')->unsigned();
			$table->foreign('enroller_id')
				->references('id')->on('users');

			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users');

			$table->decimal('amount', 10, 2)->nullable(false);
			$table->timestamp('payout_dt')->nullable();

			$table->timestamp('refund_dt')->nullable();
			$table->bigInteger('refund_by')->unsigned();
			$table->foreign('refund_by')
				->references('id')->on('users')
				->nullable();
			$table->text('notes')->nullable();
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
		Schema::drop('commissions');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
