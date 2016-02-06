@extends('beautymail::templates.sunny')

@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<h3>Hi {{$purchase->first_name}} {{$purchase->last_name}},</h3>
	<p>Thanks for your recent (product) payment</p>
	<p>These is the information regarding to your purchase: <br/>
		To access your product, please login at the link<br/>
		below using your email and password.<br/>

		You can login in <a href="{{ Config::get('app.url') . '/login' }}">{{ Config::get('app.url') . '/login' }}</a><br/><br/>

		This message will serve as your receipt.<br/>
		@foreach ($purchase->detail as $detail)
		Product: {{ $detail['product_display_name'] }}<br/>
		@endforeach
		Price: ${{$purchase->amount}} USD<br/>
		Payment Date: {{$purchase->created_at}}<br/><br/>

		Sincerely,<br>
		Maxx Fairo
	</p><br>
	@include('emails.disclaimer')
	@include('beautymail::templates.sunny.contentEnd')
@stop
