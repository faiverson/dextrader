@extends('beautymail::templates.sunny')

@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<h3>Hello {{$user->first_name}} {{$user->last_name}},</h3>
	<p> If you don't remember the password and you sent a request to change it<br/>
		Click here to reset your password: {{ url('password/reset/' . $token) }}
	</p>
	<p>This link will expire after one hour</p>
	@include('beautymail::templates.sunny.contentEnd')

	@include('beautymail::templates.sunny.button', [
            'title' => 'Change Password',
            'link' => url('password/reset/' . $token)
    ])
@stop
