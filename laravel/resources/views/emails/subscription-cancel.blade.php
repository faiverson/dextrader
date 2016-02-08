@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hi {{ $user->fullname }},<br/><br/>

		Your {{ $product->display_name }} was just cancelled by your own request or<br/>
		due to non-payment, so your {{ $product->display_name }} will be<br/>
		suspended, effective immediately.<br/><br/>

		If you believe this is a mistake, please log into your members<br/>
		area and update your credit card information right away.
	</p>
	<p>You can log in in <a href="{{ Config::get('app.url') . '/login' }}">{{ Config::get('app.url') . '/login' }}</a><br/><br/>
		Your username is: {{ $user->username }}<br>
		Your password is private to you.<br><br>
		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('beautymail::templates.sunny.contentEnd')
@stop
