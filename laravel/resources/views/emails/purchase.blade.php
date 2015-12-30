@extends('beautymail::templates.sunny')

@section('content')
	@include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Dex Trader',
        'level' => 'h1',
    ])
	@include('beautymail::templates.sunny.contentStart', ['color' => '#0000FF'])
	<h3>Hello {{$purchase->first_name}} {{$purchase->last_name}},</h3>
	<p> These is the information regarding to your purchase: <br/>
		<table>
			<tr>
				<td>Billing address:</td>
				<td>{{$purchase->billing_address}}</td>
			</tr>
			<tr>
				<td>Billing country:</td>
				<td>{{$purchase->billing_country}}</td>
			</tr>
			<tr>
				<td>Billing state:</td>
				<td>{{$purchase->billing_state}}</td>
			</tr>
			<tr>
				<td>Billing city:</td>
				<td>{{$purchase->billing_city}}</td>
			</tr>
		<tr>
			<td>Billing zip code:</td>
			<td>{{$purchase->billing_zip}}</td>
		</tr>
			<tr>
				<td>Billing phone:</td>
				<td>{{$purchase->billing_phone}}</td>
			</tr>
			<tr>
				<td>Card holder:</td>
				<td>{{$purchase->card_name}}</td>
			</tr>
			<tr>
				<td>Card number:</td>
				<td>XXXX-XXXX-XXXX-{{$purchase->card_last_four}}</td>
			</tr>
			<tr>
				@if (count($purchase->detail) === 1)
					<td>Product:</td>
					<td>{{$purchase->detail[0]['product_display_name']}}</td>
				@else
					<td>Products:</td>
					<td>
						<table>
							@foreach ($purchase->detail as $detail)
								<tr>
									<td>Product:</td>
									<td>{{$detail['product_display_name']}}</td>
								</tr>
							@endforeach
						</table>
					</td>
				@endif

				<td>{{$purchase->product_name}}</td>
			</tr>
			<tr>
				<td>Amount:</td>
				<td>{{$purchase->amount}}</td>
			</tr>
		</table>
	</p>
	@include('beautymail::templates.sunny.contentEnd')
@stop
