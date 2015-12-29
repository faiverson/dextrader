<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LiveSignalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('live_signals', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->date('signal_date')->nullable(false)->index();
			$table->string('signal_time', 6)->nullable(false);
			$table->string('expiry_time', 6)->nullable();
			$table->string('asset', 10)->nullable()->index();
			$table->string('asset_rate', 10)->nullable();
			$table->decimal('target_price', 19, 5)->signed()->nullable(false);
			$table->decimal('end_price', 19, 5)->signed()->nullable();

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
		Schema::drop('live_signals');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
