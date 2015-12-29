@extends('beautymail::templates.sunny')

@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hey {{$user->first_name}} {{$user->last_name}}<br/>
	You have a new commission! Login <a href="{{ url('/commissions') }}">here</a> to take a look!</p>
	<p> There is a new signal in the system<br/></p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
