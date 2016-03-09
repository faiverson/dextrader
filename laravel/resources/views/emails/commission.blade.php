@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<p> Congrats {{ $to->fullname }}!<br/><br/>
		@if(!empty($intermediate))
			{{ $intermediate->fullname }} just helped you earn ${{ $commission->amount }} commission by<br>
		@else
		You just earned a ${{ $commission->amount }} commission by<br>
		@endif
		selling {{ $products }} to {{ $from->fullname }}<br>
		{{ $from->fullname }} just gave you a commission...</p>

	<p>
		@if(empty($intermediate))
			Here's their info if you want to contact them. <br><br>
			Email: {{ $from->email }}
			@if ($from->phone != '')
				Phone: {{ $from->phone }}
			@endif <br><br>
			Keep up the good work!
		@endif
		<br><br>
		Sincerely,<br>
		Maxx Fairo
	</p>
	@include('emails.disclaimer')
	@include('emails.contentEnd')
@stop