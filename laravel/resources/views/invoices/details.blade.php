<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dextrader Invoice</title>
    {!! Html::style('assets/css/style.css') !!}
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="http://dextrader.com/front/assets/images/logo.png">
    </div>
    <h1>INVOICE {{$invoice->id}}</h1>
    <div id="company" class="clearfix">
        <div>Dextrader</div>
        <div>455 Foggy Heights,<br /> AZ 85004, US</div>
        <div>(602) 519-0450</div>
        <div><a href="mailto:support@dextrader.com">support@dextrader.com</a></div>
    </div>
    <div id="project">
        <div><span>To</span> {{$invoice->first_name}}</div>
        <div><span>ADDRESS</span> {{$invoice->billing_address}}, {{$invoice->zip}}, {{$invoice->country}}</div>
        <div><span>EMAIL</span> <a href="mailto:john@example.com">john@example.com</a></div>
        <div><span>DATE</span> {{ date('F d, Y', strtotime($invoice->created_at)) }}</div>
    </div>
</header>
<main>
    <table>
        <thead>
        <tr>
            <th class="service">PRODUCT</th>
            <th class="desc">DESCRIPTION</th>
            <th>PRICE</th>
        </tr>
        </thead>
        <tbody>
        @if (count($invoice->detail) === 1)
            <tr>
                <td>{{$invoice->detail[0]['product_name']}}</td>
                <td><a href="#">{{$invoice->detail[0]['product_display_name']}}</a></td>
                <td class="text-right">${{$invoice->detail[0]['product_amount']}}</td>
            </tr>
        @else
            @foreach ($invoice->detail as $detail)
                <tr>
                    <td>{{$detail['product_name']}}</td>
                    <td><a href="#">{{$detail['product_display_name']}}</a></td>
                    <td class="text-right">${{$detail['product_amount']}}</td>
                </tr>
            @endforeach
        @endif
        <tr>
            <td colspan="4" class="grand total">TOTAL</td>
            <td class="grand total">${{$invoice->amount}}</td>
        </tr>
        </tbody>
    </table>
</main>
<footer>
    Invoice was created on a computer and is valid without the signature and seal.
</footer>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dextrader Invoice</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>

        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'Bree Serif', serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <h1>
                    <a href="http://dextrader.com">
                        <img src="http://dextrader.com/front/assets/images/logo.png">
                    </a>
                </h1>
            </div>
            <div class="col-xs-6 text-right">
                <h1>INVOICE</h1>

                <h1>
                    <small>Invoice #{{$invoice->id}}</small>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-5">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>From: <a href="#">Dextrader.com</a></h4>
                    </div>
                    <div class="panel-body">
                        <p>
                            Address <br>
                            details <br>
                            more <br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xs-5 col-xs-offset-2 text-right">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>To : <a href="#">{{$invoice->first_name}} {{$invoice->last_name}}</a></h4>
                    </div>
                    <div class="panel-body">
                        <p>
                            {{$invoice->billing_address}} <br>
                            {{$invoice->billing_address2}} <br>
                            {{$invoice->zip}}, {{$invoice->city}}, {{$invoice->state}}, {{$invoice->country}} <br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- / end client details section -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>
                    <h4>Product</h4>
                </th>
                <th>
                    <h4>Description</h4>
                </th>
                <th>
                    <h4>Rate/Price</h4>
                </th>
            </tr>
            </thead>
            <tbody>
            @if (count($invoice->detail) === 1)
                <tr>
                    <td>{{$invoice->detail[0]['product_name']}}</td>
                    <td><a href="#">{{$invoice->detail[0]['product_display_name']}}</a></td>
                    <td class="text-right">${{$invoice->detail[0]['product_amount']}}</td>
                </tr>
            @else
                @foreach ($invoice->detail as $detail)
                    <tr>
                        <td>{{$detail['product_name']}}</td>
                        <td><a href="#">{{$detail['product_display_name']}}</a></td>
                        <td class="text-right">${{$detail['product_amount']}}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        <div class="row text-right">
            <div class="col-xs-2 col-xs-offset-8">
                <p>
                    <strong>
                        Total : <br>
                    </strong>
                </p>
            </div>
            <div class="col-xs-2">
                <strong>
                    ${{$invoice->amount}} <br>
                </strong>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-5">

            </div>
            <div class="col-xs-7">
                <div class="span7">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4>Contact Details</h4>
                        </div>
                        <div class="panel-body">
                            <p>
                                Email : support@dextrader.com <br><br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>