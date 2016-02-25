@extends('emails.layout')
@section('content')
	@include('emails.contentStart')
	<p> Hi {{ $user->fullname }},<br/>

		Thanks for your recent (product) monthly payment
		To access your product, please login at the link
		below using your email and password.<br/>
		@foreach ($invoice_detail as $detail)
			Product: {{ $detail->product_display_name }}<br/>
		@endforeach
		Price: ${{ $invoice->amount }} USD<br/>
		Payment Date:  @dates($invoice->created_at)  <br/><br/>

		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('emails.contentEnd')
@stop