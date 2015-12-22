<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MarketingLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// funnels URLs
		Schema::create('funnels', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable(false);
			$table->string('image')->nullable(false);
			$table->string('link')->nullable(false);
			$table->text('description')->nullable();

			$table->integer('campaign_id')->unsigned()->nullable()->default(null);
			$table->foreign('campaign_id')->references('id')->on('funnel_campaigns')
				->onUpdate('cascade')->onDelete('cascade');
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
		Schema::drop('funnels');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
