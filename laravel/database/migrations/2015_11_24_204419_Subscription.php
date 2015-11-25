<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Subscription extends Migration
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
			$table->foreign('user_id')
				->references('id')->on('users');

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')
				->references('id')->on('products');

//			$table->foreign('card_id')
//				->references('id')->on('cards');
			$table->enum('status', ['active', 'cancel', 'auto_cancel', 'admin_cancel']);
			$table->tinyInteger('attempts_billing')->default(0);
			$table->timestamp('last_billing')->nullable(false);
			$table->timestamp('next_billing')->nullable(false);
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
