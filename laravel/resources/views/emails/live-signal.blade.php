@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<p> There is a new live signal in the system<br/></p>
	@include('emails.contentEnd')
@stop
