<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('pages', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('access', 150)->nullable(false);
			$table->string('site', 50)->nullable(false);
			$table->string('domain', 50)->nullable(false)->unique('domain');
			$table->tinyInteger('active')->nullable(false)->default(1);
			$table->string('password', 200)->nullable(false);
			$table->softDeletes();
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
		Schema::dropIfExists('pages');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
