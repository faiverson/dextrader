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

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')
				->references('id')->on('products')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('card_id')->unsigned();
			$table->foreign('card_id')
				->references('id')->on('credit_cards')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')
				->references('id')->on('marketing_links')
				->onUpdate('cascade')->onDelete('cascade');

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
		Schema::drop('purchases');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
