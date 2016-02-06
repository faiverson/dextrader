@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Hi {{ $user->fullname }},<br/><br/>

		By your request, we have refunded your ${{ $amount }}<br/>
		{{ $products }} payment according to our 30 day<br/>
		refund policy. Sorry to see you go, good luck!<br/><br/>

		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('beautymail::templates.sunny.contentEnd')
@stop
