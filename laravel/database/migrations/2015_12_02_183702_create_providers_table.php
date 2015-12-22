<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable(false);
            $table->string('image', 255)->nullable();
            $table->integer('min_deposit')->nullable(false);
            $table->boolean('us_traders')->nullable(false)->default(false);
            $table->text('review')->nullable();
            $table->text('web_site')->nullable();
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
        Schema::drop('providers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
