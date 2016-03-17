<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dextrader Invoice</title>
    {!! Html::style('assets/css/style.css') !!}
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<style>
		body, h1, h2, h3, h4, h5, h6 {
			font-family: 'Bree Serif', serif;
		}
		thead:before, thead:after { display: none; }
		tbody:before, tbody:after { display: none; }
	</style>
</head>
<body>
	<div class="container">
		<header class="clearfix">
			<div class="col-md-6">
				<div id="logo"><img src="http://dextrader.com/front/assets/images/logo.png"></div>
			</div>
			<div class="col-md-6 text-right">{{ date('F d, Y', strtotime($invoice->created_at)) }}</div>
		</header>
		<div class="row ">
			<div class="col-md-6 text-left">
				<h3> {{$invoice->first_name}} {{$invoice->last_name}}</h3>
			</div>
			<div class="col-md-6 text-right">
				<h3>Invoice #{{$invoice->id}}</h3>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">

				<p><strong>Address: </strong> {{$invoice->billing_address}}, {{$invoice->billing_zip}}</p>
				@if ($invoice->billing_address2)
					<p><strong>Address 2: </strong> {{$invoice->billing_address2}}</p>
				@endif
				<p><strong>Phone: </strong> {{$invoice->billing_phone}}</p>
				<p><strong>Country: </strong> {{$invoice->billing_country}}</p>
				<p><strong>State: </strong> {{$invoice->billing_state}}, {{$invoice->billing_city}}</p>
				<p><strong>City: </strong> {{$invoice->billing_city}}</p>
				<p><strong>Email: </strong> <a href="#">{{$invoice->email}}</a></p>
				<p><strong>Credit Card name: </strong> {{ $invoice->card_name }}</p>
				<p><strong>Card type: </strong> {{ ucfirst($invoice->card_network) }}</p>
				<p><strong>Card number: </strong> **** **** **** {{ $invoice->card_last_four }} </p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped">
					<thead>
					<tr>
						<th class="active service">PRODUCTS</th>
						<th class="active text-right">PRICE</th>
					</tr>
					</thead>
					<tbody>
					@if (count($invoice->detail) === 1)
						<tr>
							<td><a href="#">{{$invoice->detail[0]['product_display_name']}}</a></td>
							<td class="text-right">${{$invoice->detail[0]['product_amount']}}</td>
						</tr>
					@else
						@foreach ($invoice->detail as $detail)
							<tr>
								<td><a href="#">{{$detail['product_display_name']}}</a></td>
								<td class="text-right">${{$detail['product_amount']}}</td>
							</tr>
						@endforeach
					@endif
					<tr>
						<td class="info grand total"><strong>Total</strong></td>
						<td class="info grand total text-right"><strong>${{$invoice->amount}}</strong></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-6"></div>
		</div>
		<hr />
		<footer class="small clearfix">
			<div class="row">
				<div class="col-md-12 text-right">
					<div>Dextrader</div>
					<div>455 Foggy Heights,<br /> AZ 85004, US</div>
					<div>(602) 519-0450</div>
					<div>Contact us: <a href="mailto:support@dextrader.com">support@dextrader.com</a></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					<i>Invoice was created on a computer and is valid without the signature and seal.</i>
				</div>
			</div>
		</footer>
	</div>

</body>
</html>
