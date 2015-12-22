<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Purchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('purchases', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');

			$table->bigInteger('transaction_id')->unsigned();
			$table->foreign('transaction_id')
				->references('id')->on('transactions')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')
				->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('tag_id')->unsigned()->nullable()->default(null);
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('invoice_id')->unsigned();
			$table->foreign('invoice_id')
				->references('id')->on('invoices')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('subscription_id')->unsigned();
			$table->foreign('subscription_id')
				->references('id')->on('subscriptions')
				->onUpdate('cascade')->onDelete('cascade');


			$table->dateTime('refunded_at')->nulleable(false)->default(null);
			$table->dateTime('cancelled_at')->nulleable(false)->default(null);

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
		Schema::drop('purchases');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
