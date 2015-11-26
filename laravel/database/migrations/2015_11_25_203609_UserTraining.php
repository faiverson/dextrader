<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserTraining extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('users_trainings', function (Blueprint $table) {
			$table->bigInteger('user_id')->unsigned();
			$table->foreign('user_id')
				->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');

			$table->integer('training_id')->unsigned();
			$table->foreign('training_id')
					->references('id')->on('trainings')
				->onUpdate('cascade')->onDelete('cascade');

			$table->enum('type', ['certification', 'pro', 'affiliates'])->nullable(false);
			$table->timestamp('created_at')->nullable(false);
			$table->primary(['user_id', 'training_id']);
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
		Schema::drop('users_trainings');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
