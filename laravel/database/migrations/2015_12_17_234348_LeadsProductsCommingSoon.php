<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LeadsProductsCommingSoon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('leads_products_comming_soon', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('products')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('email')->nullable(false);
			$table->string('phone')->nullable();
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
		Schema::drop('leads_products_comming_soon');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
