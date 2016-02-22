<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MarketingStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('marketing_stats', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned()->nullable()->default(null);
			$table->foreign('user_id')->references('id')->on('users');

			$table->string('tag', 80)->nullable();
			$table->string('funnel', 80)->nullable();

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')
				->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('tag_id')->unsigned()->nullable()->default(null);
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('hits')->default(0);
			$table->bigInteger('unique_hits')->default(0);
			$table->bigInteger('leads')->default(0);
			$table->bigInteger('ib')->default(0);
			$table->bigInteger('pro')->default(0);
			$table->bigInteger('na')->default(0);
			$table->bigInteger('fx')->default(0);
			$table->bigInteger('academy')->default(0);
			$table->decimal('income', 10, 2)->default(0);
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
		Schema::drop('marketing_stats');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
