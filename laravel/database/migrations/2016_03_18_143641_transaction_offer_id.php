<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB as DB;

class TransactionOfferId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::statement('ALTER TABLE special_offers ENGINE = InnoDB');

		Schema::table('transactions', function ($table) {
			$table->unsignedInteger('offer_id')->after('tag')->nullable()->default(null);
			$table->foreign('offer_id')
				->references('id')->on('special_offers')
				->onUpdate('set null')->onDelete('set null');
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
