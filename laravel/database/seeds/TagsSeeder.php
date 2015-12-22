<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Tag::unguard();
        Tag::create([
			'title' => 'facebook'
		]);
		Tag::create([
			'title' => 'twitter'
		]);
		Tag::create([
			'title' => 'checkout page'
		]);
		Tag::reguard();
    }
}
