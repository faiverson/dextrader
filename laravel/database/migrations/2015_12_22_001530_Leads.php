<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Leads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('leads', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->integer('funnel_id')->unsigned();
			$table->foreign('funnel_id')->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');

			$table->integer('tag_id')->unsigned()->nullable();
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('first_name', 50)->nullable(false);
			$table->string('last_name', 50)->nullable(false);
			$table->string('username', 50)->nullable(false)->unique('username');
			$table->string('email')->nullable(false)->unique('email');
			$table->string('phone', 15);
			$table->string('ip_address', 25)->nullable();
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
		Schema::drop('leads');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
