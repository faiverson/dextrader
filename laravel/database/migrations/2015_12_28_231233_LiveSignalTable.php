<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LiveSignalTable extends Migration
{
    /**
     * Run the migrations.
	 * MT_ID = MT = MetaTrader && ID = to the ID that was used to go into his separate database so we can track when an update happens
	 * TRADE TYPE VALUES BELOW:
	 * M = Minute Value ( 1 || 5 || 15 || 30 )
	 * H = Hour Value ( 1 || 4 )
	 * D = Day Value ( == 1 )
	 * W = Week Value ( == 1 )
	 * MN = Month ( == 1 )
	 *
	 * DIRECTION ( 0 == BUY || 1 == SELL )
	 * SYMBOL ( This is the stock/symbol that the user will understand, IE: GBPUSD || USDJPY )
	 * OPEN_PRICE ( This is the current price as of this signal )
	 * TARGET_PRICE ( == 0 for now, this will get updated later )
	 * CLOSE_PRICE ( This comes in on the update api, this will be the price that this signal closed with when the signal expired )
	 * WINLOSS ( 0 == LOSS || 1 == WIN )
	 *
     * @return void
     */
    public function up()
    {
		Schema::create('ib_signals', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('mt_id')->signed();
			$table->dateTime('signal_time')->nullable();
			$table->dateTime('expiry_time')->nullable();
			$table->tinyInteger('direction')->default(0);
			$table->string('asset', 10)->nullable()->index(); // symbol
			$table->string('trade_type', 4)->nullable();
			$table->decimal('open_price', 11, 5)->signed()->nullable();
			$table->decimal('target_price', 11, 5)->signed()->nullable();
			$table->decimal('close_price', 11, 5)->signed()->nullable();
			$table->dateTime('close_time')->nullable();
			$table->tinyInteger('winloss');

			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create('fx_signals', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('mt_id')->signed();
			$table->dateTime('signal_time')->nullable();
			$table->dateTime('expiry_time')->nullable();
			$table->tinyInteger('direction')->default(0);
			$table->string('asset', 10)->nullable()->index(); // symbol
			$table->string('trade_type', 4)->nullable();
			$table->decimal('open_price', 11, 5)->signed()->nullable();
			$table->decimal('target_price', 11, 5)->signed()->nullable();
			$table->decimal('close_price', 11, 5)->signed()->nullable();
			$table->dateTime('close_time')->nullable();
			$table->tinyInteger('winloss');

			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create('na_signals', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('mt_id')->signed();
			$table->dateTime('signal_time')->nullable();
			$table->dateTime('expiry_time')->nullable();
			$table->tinyInteger('direction')->default(0);
			$table->string('asset', 10)->nullable()->index(); // symbol
			$table->string('trade_type', 4)->nullable();
			$table->decimal('open_price', 11, 5)->signed()->nullable();
			$table->decimal('target_price', 11, 5)->signed()->nullable();
			$table->decimal('close_price', 11, 5)->signed()->nullable();
			$table->dateTime('close_time')->nullable();
			$table->tinyInteger('winloss');

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
		Schema::drop('ib_signals');
		Schema::drop('fx_signals');
		Schema::drop('na_signals');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
