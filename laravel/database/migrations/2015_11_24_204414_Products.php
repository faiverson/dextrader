<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('products', function (Blueprint $table) {
			$table->smallIncrements('id');
			$table->string('name');
			$table->string('display_name');
			$table->decimal('amount', 10, 2)->nullable(false);
			$table->decimal('discount', 10, 2)->default(0);
			$table->tinyInteger('active')->nullable(false)->default(1);
			$table->timestamps();
			$table->softDeletes();
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
		Schema::drop('products');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
