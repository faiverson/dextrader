<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Trainings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('trainings', function (Blueprint $table) {
			$table->increments('id');
			$table->string('video_id', 30)->nullable(false);
			$table->string('title', 150)->nullable();
			$table->string('filename', 150)->nullable(false);
			$table->text('description')->nullable();
			$table->string('time', 8)->nullable()->default('00:00');
			$table->integer('unlock_at')->nullable()->default(0); //seconds
			$table->enum('type', ['certification', 'pro', 'affiliates'])->nullable(false);
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
		Schema::drop('trainings');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
