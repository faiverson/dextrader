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

			$table->string('first_name', 50)->nullable(false);
			$table->string('last_name', 50)->nullable(false);
			$table->string('email')->nullable(false);

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');
			$table->decimal('amount', 10, 2)->signed()->nullable(false);

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')
				->references('id')->on('products')
				->onUpdate('cascade')->onDelete('cascade');
			$table->decimal('product_amount', 10, 2)->signed()->nullable(false);
			$table->decimal('product_discount', 10, 2)->signed()->nullable(false);
			$table->string('product_name', 100)->nullable(false);

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')
				->references('id')->on('marketing_links')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('card_id')->unsigned()->nullable(false);
			$table->string('card_name', 150)->nullable(false);
			$table->smallInteger('card_exp_month')->nullable(false);
			$table->smallInteger('card_exp_year')->nullable(false);
			$table->integer('card_first_six')->nullable(false);
			$table->integer('card_last_four')->nullable(false);
			$table->string('card_network', 15)->nullable(false);

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
			$table->tinyInteger('paid')->nullable(false)->default(0);
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
