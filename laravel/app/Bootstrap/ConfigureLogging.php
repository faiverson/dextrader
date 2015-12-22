<?php

namespace App\Bootstrap;

use Illuminate\Log\Writer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class ConfigureLogging extends BaseConfigureLogging {

	protected function configureCustomHandler(Application $app, Writer $log)
	{
		$log->useDailyFiles(
			$app->storagePath().'/logs/laravel.log',
			$app->make('config')->get('app.log_max_files', 5)
		);
	}
}