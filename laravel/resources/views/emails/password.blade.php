@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<h3>Hello {{$user->first_name}} {{$user->last_name}},</h3>
	<p> If you don't remember the password and you sent a request to change it<br/>
		Click here to reset your password: <br/>
		{{ Config::get('dextrader.base_url') . '/password/reset/' . $token }}
	</p>
	<p>This link will expire after one hour</p>
	@include('emails.button', [
            'title' => 'Change Password',
            'link' => Config::get('dextrader.base_url') . '/password/reset/' . $token
    ])
	@include('emails.contentEnd')
@stop
