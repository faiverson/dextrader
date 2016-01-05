<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CampaignTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('campaign_tags', function (Blueprint $table) {
			$table->increments('id');
			$table->string('tag')->nullable(false)->index();
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');
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
		Schema::drop('campaign_tags');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
