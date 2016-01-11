<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('hits', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('ip_address', 25)->nullable();

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')
				->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('tag_id')->unsigned()->nullable()->default(null);
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->smallInteger('product_id')->unsigned()->nullable()->default(null);
			$table->foreign('product_id')
				->references('id')->on('products')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('tag', 80)->nullable();
			$table->string('enroller', 80)->nullable();

			$table->text('info')->nullable();
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
		Schema::drop('hits');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
