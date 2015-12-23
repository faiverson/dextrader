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
			'tag' => 'facebook'
		]);
		Tag::create([
			'tag' => 'twitter'
		]);
		Tag::create([
			'tag' => 'checkout page'
		]);
		Tag::reguard();
    }
}
