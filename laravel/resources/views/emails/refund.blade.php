@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hi {{ $user->fullname }},<br/>

		It is a shame but your purchase has been refunded! <br>
	</p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
