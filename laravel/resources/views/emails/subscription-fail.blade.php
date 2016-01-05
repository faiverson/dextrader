@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hi {{ $user->fullname }},<br/>

		It is the {{ $subscription->attempts_billing }} of 3 attempts to charge the payment of the month.<br>
		After that the subscription will be cancel.<br>
	</p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
