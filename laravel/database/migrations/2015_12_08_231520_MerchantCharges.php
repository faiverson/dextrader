<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('gateway_transactions', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');

			$table->bigInteger('purchase_id')->unsigned();
			$table->foreign('purchase_id')->references('id')->on('purchases');

			$table->string('status', 50)->nullable();
			$table->string('authcode')->nullable();
			$table->string('transactionid')->nullable();
			$table->string('orderid')->nullable();

			$table->string('avsresponse', 250)->nullable();
			$table->string('cvvresponse', 250)->nullable();
			$table->string('type', 50)->nullable();
			$table->string('response_code', 50)->nullable();
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
		Schema::drop('gateway_transactions');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');    }
}
