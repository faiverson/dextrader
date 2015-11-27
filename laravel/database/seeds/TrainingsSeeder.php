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
			'filename' => 'ubvV498pyIM.mp4',
			'description' => 'Music video by No Doubt performing It\'s My Life. (C) 2003 Interscope Records',
			'type' => 'pro'
		]);
	}

	public function addCertification()
	{
		Training::create([
			'title' => 'Belgrano',
			'time' => '04:32',
			'video_id' => 'i5-6i6medlI',
			'filename' => 'i5-6i6medlI.mp4',
			'description' => 'Music video by Pirates (C) 2009 Kempes',
			'type' => 'certification',
			'unlock_at' => 50
		]);

		Training::create([
			'title' => 'Pirates',
			'time' => '03:32',
			'video_id' => '9A1GlYP3Dyk',
			'filename' => '9A1GlYP3Dyk.mp4',
			'description' => 'Music video by Pirates (C) 2012 Kempes',
			'type' => 'certification',
			'unlock_at' => 23
		]);

		Training::create([
			'title' => 'Light blues',
			'time' => '02:32',
			'video_id' => 'Dy-rOiBS_T4',
			'filename' => 'Dy-rOiBS_T4.mp4',
			'description' => 'Music video by Pirates (C) 2009 Kempes',
			'type' => 'certification',
			'unlock_at' => 120
		]);
	}

	public function addAffiliates()
	{
		Training::create([
			'title' => 'Hello laidy',
			'time' => '02:33',
			'video_id' => 'ncnJpYTi-hY',
			'filename' => 'ncnJpYTi-hY.mp4',
			'description' => 'Lorem Ipsum',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Favourite Game',
			'time' => '12:33',
			'video_id' => 'Qq-I4orlEhE',
			'filename' => 'Qq-I4orlEhE.mp4',
			'description' => 'Lorem Ipsum 2',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Sweet Child',
			'time' => '08:22',
			'video_id' => 'ubvV498pyIM',
			'filename' => 'ubvV498pyIM.mp4',
			'description' => 'Music video by No Doubt performing It\'s My Life. (C) 2003 Interscope Records',
			'type' => 'affiliates'
		]);
	}
}