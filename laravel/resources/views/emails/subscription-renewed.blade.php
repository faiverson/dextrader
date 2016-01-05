@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hi {{ $user->fullname }},<br/>

		There is a new invoice because your subscription was paid. You can see the payment in the site!
	</p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
