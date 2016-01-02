<?php
namespace App\Helpers;

use Config;

final class Encrypt
{
	private static function SALT() {
		return Config::get('dextrader.salt');
	}

	public static function encrypt($text)
	{
		$secure_key = hash('sha256', self::SALT(), true);

		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secure_key, $text, MCRYPT_MODE_ECB));
	}

	public static function decrypt($text)
	{
		$secure_key = hash('sha256', self::SALT(), true);

		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secure_key, base64_decode($text), MCRYPT_MODE_ECB));
	}

	public static function encrypt_json($arr)
	{
		$text = json_encode($arr);
		return self::encrypt_text($text);
	}

	public static function decrypt_json($text)
	{
		$text = self::decrypt_text($text);
		return json_decode($text, true);
	}
}