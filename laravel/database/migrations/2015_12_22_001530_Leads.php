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

			$table->bigInteger('user_id')->unsigned()->nullable()->default(null);
			$table->foreign('user_id')->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('funnel_id')->unsigned()->nullable()->default(null);
			$table->foreign('funnel_id')->references('id')->on('funnels')
				->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('enroller_id')->unsigned()->nullable()->default(null);
			$table->foreign('enroller_id')->references('id')->on('users');

			$table->integer('tag_id')->unsigned()->nullable()->default(null);
			$table->foreign('tag_id')
				->references('id')->on('campaign_tags')
				->onUpdate('cascade')->onDelete('cascade');

			$table->string('first_name', 50)->nullable();
			$table->string('last_name', 50)->nullable();
			$table->string('username', 50)->nullable();
			$table->string('email')->nullable(false)->unique('email');
			$table->string('phone', 15)->nullable();
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
