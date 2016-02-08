@extends('beautymail::templates.sunny')
@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
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
	@include('beautymail::templates.sunny.contentEnd')
@stop
