<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommissionTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('commissions_total', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->decimal('paid', 10, 2)->signed()->nullable(false)->default(0);
			$table->decimal('ready', 10, 2)->signed()->nullable(false)->default(0);
			$table->decimal('pending', 10, 2)->signed()->nullable(false)->default(0);
			$table->decimal('holdback', 10, 2)->signed()->nullable(false)->default(0);
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
		Schema::drop('commissions_total');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
