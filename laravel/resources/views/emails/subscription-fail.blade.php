@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<p> Hi {{ $user->fullname }},<br/><br/>

		It seems that your {{ $product->display_name }} payment has failed.<br/><br/>

		We will try again in 2 days, If your payment fails 3 times, your<br/>
		{{ $product->display_name }} subscription will be cancelled and you will<br/>
		no longer have access to using it.<br/><br/>

		To avoid that from happening, please log into your members<br/>
		area and update your credit card information right away.<br/><br/>

		You can log in in <a href="{{ Config::get('app.url') . '/login' }}">{{ Config::get('app.url') . '/login' }}</a><br/><br/>

		Your username is: {{ $product->username }}<br/>
		Your password is private to you.<br/><br/>

		...and update your CC information on file by visiting your<br/>
		account profile in the top right corner of the back office.<br/><br/>

		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('emails.contentEnd')
@stop
