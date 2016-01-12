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

			$table->bigInteger('from_user_id')->unsigned();
			$table->foreign('from_user_id')->references('id')->on('users');

			$table->bigInteger('to_user_id')->unsigned();
			$table->foreign('to_user_id')->references('id')->on('users');

			$table->bigInteger('invoice_id')->unsigned();
			$table->foreign('invoice_id')->references('id')->on('invoices');

			$table->decimal('amount', 10, 2)->signed()->nullable(false);
			$table->enum('status', ['pending', 'paid', 'refund', 'ready'])->default('pending');
			$table->enum('type', ['enroller', 'parent', 'admin', 'refund'])->default('enroller');
			$table->dateTime('payout_dt')->nullable();
			$table->dateTime('refund_dt')->nullable();
			$table->dateTime('holdback_dt')->nullable();
			$table->bigInteger('refund_by')->unsigned()->nullable();
			$table->foreign('refund_by')->references('id')->on('users');
			$table->smallInteger('holdback_paid')->unsigned()->default(0);

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
