<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->decimal('prev_balance', 10, 2)->signed()->nullable(false);
			$table->decimal('amount', 10, 2)->signed()->nullable(false);
			$table->decimal('balance', 10, 2)->signed()->nullable(false);
			$table->string('ledger_type');
			$table->text('info')->nullable();
			$table->dateTime('paid_dt')->index();
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
		Schema::drop('payments');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
