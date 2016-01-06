<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('first_name', 50)->nullable(false);
			$table->string('last_name', 50)->nullable(false);
			$table->string('email')->nullable(false);

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');
			$table->decimal('amount', 10, 2)->signed()->nullable(false);

			$table->integer('funnel_id')->unsigned()->nullable()->default(null);
			$table->foreign('funnel_id')
				->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('tag_id')->unsigned()->nullable()->default(null);
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('transaction_id')->unsigned();
			$table->foreign('transaction_id')
				->references('id')->on('transactions')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('card_id')->unsigned()->nullable(false);
			$table->string('card_name', 150)->nullable(false);
			$table->smallInteger('card_exp_month')->nullable(false);
			$table->smallInteger('card_exp_year')->nullable(false);
			$table->string('card_first_six', 6)->nullable(false);
			$table->string('card_last_four', 4)->nullable(false);
			$table->string('card_network', 15)->nullable(false);

			$table->bigInteger('billing_address_id')->unsigned();
			$table->foreign('billing_address_id')
				->references('id')->on('billing_address')
				->onUpdate('cascade')->onDelete('cascade');
			$table->string('billing_address', 200)->nullable(false);
			$table->string('billing_address2', 200)->nullable();
			$table->string('billing_city', 100)->nullable(false);
			$table->string('billing_state', 100)->nullable(false);
			$table->string('billing_country', 100)->nullable(false);
			$table->integer('billing_zip')->nullable(false);
			$table->string('billing_phone', 15);

			$table->text('info')->nullable();
			$table->string('ip_address', 25)->nullable();

			$table->dateTime('refunded_at')->nulleable(false)->default(null);
			$table->dateTime('cancelled_at')->nulleable(false)->default(null);
			$table->timestamps();
			$table->index(['refunded_at', 'cancelled_at']);
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
		Schema::drop('invoices');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
