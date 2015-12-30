@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<p> Congrats {{ $to->fullname }}!<br/>

		{{ $from->fullname }} just gave you a commission...<br>
		Seriously, you just earned a commission from <br>
		{{ $from->first_name }} ...without doing anything at all! <br><br>
		Here's their info if you want to welcome them. <br><br>
		Email: {{ $from->email }} <br>
		@if ($from->phone != '')
			Phone: {{ $from->phone }} <br><br>
		@endif
		Have a great day! <br>
	</p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
