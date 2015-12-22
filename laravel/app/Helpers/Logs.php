<?php
namespace App\Helpers;

use Monolog;

class Logs {

	/**
	 * @param $path path to the file with subfolders
	 * @param bool $download if you want to download or show in the browser if it's possible
	 * @return file video
	 */
	public static function save($filename, $info, $extra)
	{
		$log = new Monolog\Logger(__METHOD__);
		$log->pushHandler(new Monolog\Handler\StreamHandler(storage_path().'/logs/' . $filename . '.log'));
		$log->addInfo($info, $extra);
	}
}