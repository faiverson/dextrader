<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoicesDetailTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices_detail', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('invoice_id')->unsigned();
			$table->foreign('invoice_id')->references('id')->on('invoices')
				  ->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('subscription_id')->unsigned();
			$table->foreign('subscription_id')
				->references('id')->on('subscriptions')
				->onUpdate('cascade')->onDelete('cascade');

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('products')
				->onUpdate('cascade')->onDelete('cascade');
			$table->string('product_name', 100)->nullable(false);
			$table->string('product_display_name', 100)->nullable(false);
			$table->decimal('product_amount', 10, 2)->signed()->nullable(false);
			$table->decimal('product_discount', 10, 2)->signed()->nullable(false);

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
		Schema::drop('invoices_detail');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
