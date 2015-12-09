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
		Schema::create('leads', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')->references('id')->on('marketing_links')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('first_name', 50)->nullable(false);
			$table->string('last_name', 50)->nullable(false);
			$table->string('username', 50)->nullable(false)->unique('username');
			$table->string('email')->nullable(false)->unique('email');
			$table->string('phone', 15);
			$table->string('ip', 15);

			$table->timestamps();
		});

		Schema::create('merchant_charges', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->nullable();

			$table->bigInteger('lead_id')->unsigned();
			$table->foreign('lead_id')->references('id')->on('users')->nullable();

			$table->bigInteger('card_id')->unsigned();
			$table->foreign('card_id')->references('id')->on('credit_cards')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('status', 50)->nullable();
			$table->string('response', 250)->nullable();
			$table->string('capture_status', 250)->nullable();
			$table->integer('auth_number')->nullable();
			$table->string('amount')->nullable();
			$table->integer('last_four')->nullable();

//			$table->dateTime('refunded_at')->nulleable(false)->default(null);
//			$table->integer('refunded_id')->unsigned();
//			$table->foreign('refunded_id')->references('id')->on('refunds')->nullable();
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
        //
    }
}
