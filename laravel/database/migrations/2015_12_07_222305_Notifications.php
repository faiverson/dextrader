<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Notifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('notifications', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			// we probably need more types
			$table->enum('type', ['global', 'system', 'purchases', 'commissions', 'messages'])->default('global')->nulleable(false);

			$table->string('title', 200)->nulleable(false);
			$table->tinyInteger('viewed')->default(0)->nulleable(false);
			$table->json('data');
			$table->timestamps();

			$table->index(['type', 'viewed']);
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
		Schema::dropIfExists('notifications');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
