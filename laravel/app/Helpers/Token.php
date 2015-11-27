<?php

namespace App\Helpers;

use JWTAuth;
use User;

class Token {

	public static function getId($request)
	{
		$payload = JWTAuth::setRequest($request)->getPayload();
		return $payload['sub'];
	}

	public static function getPayload($request)
	{
		$payload = JWTAuth::setRequest($request)->getPayload();
		return $payload->toArray();
	}

	public static function add($user_id)
	{
		$user = User::with('roles.permissions')->find($user_id);
		$customClaims = $user->toArray();
		$customClaims['iss'] = 'login';
		$customClaims['exp'] = strtotime('+7 days', time());
		unset($customClaims['id']);
		return JWTAuth::fromUser($user, $customClaims);
	}

	public static function refresh($request)
	{
		$old_token = JWTAuth::setRequest($request)->getToken();
		$payload = JWTAuth::setRequest($request)->getPayload();
		$user = User::with('roles.permissions')->find($payload['sub']);
		$customClaims = $user->toArray();
		$customClaims['iss'] = 'refresh';
		$customClaims['exp'] = strtotime('+7 days', time());
		unset($customClaims['id']);
		$token = JWTAuth::fromUser($user, $customClaims);
		if($token) {
			JWTAuth::invalidate($old_token);
		}
		return $token;
	}

	public static function deprecate($request)
	{
		$token = JWTAuth::setRequest($request)->getToken();
		return JWTAuth::invalidate($token);
	}
}