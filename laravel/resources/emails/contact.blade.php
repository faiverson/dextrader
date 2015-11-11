@extends('beautymail::templates.widgets')
@section('header')
    <h1 class="primary">{{ $site }}</h1>
@stop
@section('content')
    @include('beautymail::templates.widgets.articleStart')
    <h3 class="secondary">{{ $subject }}</h3>
    <p> {{ $msg }} </p>
    @include('beautymail::templates.widgets.articleEnd')
    @include('beautymail::templates.widgets.newfeatureStart')
        <h3 class="secondary">Contact data</h3>
        @if ($name)
            <p>Name: {{ $name }}</p>
        @endif
        @if ($phone)
            <p>Phone: {{ $phone }}</p>
        @endif
        @if ($email)
            <p>Email {{ $email }}</p>
        @endif
    @include('beautymail::templates.widgets.newfeatureEnd')
@stop
@section('footer')
    <tr>
        <td width="100%" class="logocell">
			{{ $site }} - Copyright &copy;
        </td>
    </tr>
@stop
