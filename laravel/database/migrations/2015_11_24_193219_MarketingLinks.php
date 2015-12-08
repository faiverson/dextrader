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
		Schema::create('marketing_links', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable(false);
			$table->string('image')->nullable(false);
			$table->string('link')->nullable(false);
			$table->text('description')->nullable();
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
		Schema::drop('marketing_links');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}
