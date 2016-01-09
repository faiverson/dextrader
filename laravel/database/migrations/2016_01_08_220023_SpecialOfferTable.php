<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpecialOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('special_offers', function (Blueprint $table) {
			$table->increments('id');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')->references('id')->on('funnels');

			$table->smallInteger('product_id')->unsigned();
			$table->foreign('product_id')->references('id')->on('products');

			$table->decimal('amount', 8, 2)->signed()->nullable(false);
			$table->date('ending_dt')->nullable();
			$table->integer('countdown')->nullable()->default(0); // in seconds

			$table->softDeletes();
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
		Schema::drop('special_offers');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
