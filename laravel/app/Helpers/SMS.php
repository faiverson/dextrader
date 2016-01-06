<?php

namespace App\Helpers;

use twilio\sdk\Services\Twilio;
use Config;

class SMS {

	public static function send($phone, $message = '')
	{
		$client = new Services_Twilio(Config::get('twilio.sid'), Config::get('twilio.token'));
		dd($client);
		$message = $client->account->messages->sendMessage(
			Config::get('twilio.phone'), // From a valid Twilio number
			$phone, // Text this number
			$message
		);
dd($message);
		return $message->sid;
	}

}