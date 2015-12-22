<?php

namespace App\Helpers;

use JWTAuth;
use User;
use Page;
use JWTFactory;

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

	public static function add($user_id, $expiration = '+7 days', $iss = 'user')
	{
		$user = User::with('roles.permissions')->find($user_id);
		$customClaims = $user->toArray();
		$customClaims['iss'] = $iss;
		$customClaims['exp'] = strtotime($expiration, time());
		unset($customClaims['id']);
		return JWTAuth::fromUser($user, $customClaims);
	}

	public static function page($page_id, $expiration = '+7 days', $iss = 'page')
	{
		$page = Page::find($page_id);
		$customClaims = $page->toArray();
		$customClaims['sub'] = $customClaims['page_id'];
		$customClaims['iss'] = $iss;
		$customClaims['exp'] = strtotime($expiration, time());
		unset($customClaims['page_id']);
		$payload = JWTFactory::make($customClaims);
		return JWTAuth::encode($payload)->get();
	}

	public static function getPage($request)
	{
		$token = JWTAuth::setRequest($request)->getToken();
		if(empty($token)) {
			return false;
		}
		$payload = JWTAuth::getPayload($token);
		return $payload['sub'];
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