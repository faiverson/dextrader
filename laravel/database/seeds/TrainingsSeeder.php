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
			'title' => 'Welcome To Dex IB',
			'time' => '00:01:54',
			'video_id' => 'Tgm7ZpwuVfA',
			'filename' => 'Tgm7ZpwuVfA.mp4',
			'description' => 'Music video by Pirates (C) 2009 Kempes',
			'type' => 'certification',
			'unlock_at' => 90
		]);

		Training::create([
			'title' => 'Risk and Money Management',
			'time' => '00:18:26',
			'video_id' => 'aKUGMvc_KNo',
			'filename' => 'aKUGMvc_KNo.mp4',
			'description' => 'Music video by Pirates (C) 2012 Kempes',
			'type' => 'certification',
			'unlock_at' => 1020
		]);
	}

	public function addAffiliates()
	{
		Training::create([
			'title' => 'Introduction',
			'time' => '01:54',
			'video_id' => 'Tgm7ZpwuVfA',
			'filename' => 'Tgm7ZpwuVfA.mp4',
			'description' => 'Introduction',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Social Media',
			'time' => '18:27',
			'video_id' => 'aKUGMvc_KNo',
			'filename' => 'aKUGMvc_KNo.mp4',
			'description' => 'Social Media',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Emailing Leads',
			'time' => '00:02:24',
			'video_id' => 'CEwp3klqfc8',
			'filename' => 'CEwp3klqfc8.mp4',
			'description' => 'Emailing Leads',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Facebook Events',
			'time' => '00:06:07',
			'video_id' => '7D_4xe65RgI',
			'filename' => '7D_4xe65RgI.mp4',
			'description' => 'Facebook Events',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Twitter Crowdfinder',
			'time' => '00:05:05',
			'video_id' => 'p87mxQN0xe4',
			'filename' => 'p87mxQN0xe4.mp4',
			'description' => 'Twitter Crowdfinder',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Creating Videos',
			'time' => '00:02:04',
			'video_id' => 'OnoleN8QD0M',
			'filename' => 'OnoleN8QD0M.mp4',
			'description' => 'Creating Videos',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Posting Banners',
			'time' => '00:02:38',
			'video_id' => 'c3tcPE_K1vc',
			'filename' => 'c3tcPE_K1vc.mp4',
			'description' => 'Posting Banners',
			'type' => 'affiliates'
		]);
		Training::create([
			'title' => 'Referring Affiliates',
			'time' => '00:02:06',
			'video_id' => 'wBeXpSp5wC0',
			'filename' => 'wBeXpSp5wC0.mp4',
			'description' => 'Referring Affiliates',
			'type' => 'affiliates'
		]);
	}
}
