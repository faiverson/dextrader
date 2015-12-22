<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FunnelCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('funnel_campaigns', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable(false);
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
		Schema::drop('funnel_campaigns');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
