<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ZipCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('transactions', function ($table) {
			$table->string('billing_zip', 15)->change();
		});

		Schema::table('invoices', function ($table) {
			$table->string('billing_zip', 15)->change();
		});

		Schema::table('billing_address', function ($table) {
			$table->string('zip', 15)->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
