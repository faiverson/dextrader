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
			$table->string('title')->nullable(false)->index();
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
