@extends('beautymail::templates.widgets')
@section('header')
	<h1 class="primary">Dex Trader</h1>
@stop
@section('content')
	@include('beautymail::templates.widgets.articleStart')
	<h1 class="secondary">Hello {{$user->first_name}} {{$user->last_name}},</h1>
	<p> If you don't remember the password and you sent a request to change it<br/>
		Click here to reset your password: {{ url('password/reset/' . $token) }}
	@include('beautymail::templates.widgets.articleEnd')
@stop
@section('footer')
	<tr>
		<td width="100%" class="logocell">
			Dex Trader - All right reserved
		</td>
	</tr>
@stop
