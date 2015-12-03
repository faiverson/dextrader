<?php

namespace App\Bootstrap;

use Illuminate\Log\Writer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Request;
class ConfigureLogging extends BaseConfigureLogging {

	protected function configureCustomHandler(Application $app, Writer $log)
	{
//		$filename = str_replace('/', '-', $app->request->path());
//		$filename = str_replace('api-', '', $filename);
//		$log->useDailyFiles(
//			$app->storagePath().'/logs/'.$filename.'.log',
//			$app->make('config')->get('app.log_max_files', 5)
//		);
	}
}