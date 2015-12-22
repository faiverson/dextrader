<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('subscriptions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');

			$table->decimal('amount', 10, 2)->signed()->nullable(false);

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('products');

			$table->bigInteger('card_id')->unsigned();
			$table->foreign('card_id')->references('id')->on('credit_cards');

			$table->bigInteger('billing_address_id')->unsigned();
			$table->foreign('billing_address_id')->references('id')->on('billing_address');

			$table->enum('status', ['active', 'cancel', 'auto_cancel', 'admin_cancel'])->default('active');
			$table->tinyInteger('attempts_billing')->default(0);
			$table->date('last_billing')->nullable(false);
			$table->date('next_billing')->nullable(false);
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
		Schema::drop('subscriptions');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
