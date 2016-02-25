@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<p> Hi {{ $user->fullname }},<br/><br/>

		By your request, we have refunded your ${{ $amount }}<br/>
		{{ $products }} payment according to our 30 day<br/>
		refund policy. Sorry to see you go, good luck!<br/><br/>

		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('emails.contentEnd')
@stop
