<?php

use Illuminate\Database\Seeder;
use App\Models\Training;

class TrainingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$this->command->info("Creating Trainings");
		Training::unguard();
		$this->addCertification();
		$this->addAffiliates();
		$this->addPro();
		Training::reguard();
		$this->command->info("Trainings finished");
    }

	public function addPro()
	{
		Training::create([
			'title' => 'Sweet Child',
			'time' => '08:22',
			'video_id' => 'ubvV498pyIM',
			'description' => 'Music video by No Doubt performing It\'s My Life. (C) 2003 Interscope Records',
			'type' => 'pro'
		]);
	}

	public function addCertification()
	{
		Training::create([
			'title' => 'Sweet Child',
			'time' => '08:22',
			'video_id' => 'ubvV498pyIM',
			'description' => 'Music video by No Doubt performing It\'s My Life. (C) 2003 Interscope Records',
			'type' => 'certification',
			'unlock_at' => 33
		]);
	}

	public function addAffiliates()
	{
		Training::create([
			'title' => 'Hello laidy',
			'time' => '02:33',
			'video_id' => 'ncnJpYTi-hY',
			'description' => 'Lorem Ipsum',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Favourite Game',
			'time' => '12:33',
			'video_id' => 'Qq-I4orlEhE',
			'description' => 'Lorem Ipsum 2',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Sweet Child',
			'time' => '08:22',
			'video_id' => 'ubvV498pyIM',
			'description' => 'Music video by No Doubt performing It\'s My Life. (C) 2003 Interscope Records',
			'type' => 'affiliates'
		]);
	}
}
