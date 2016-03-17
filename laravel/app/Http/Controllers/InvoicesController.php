<?php

namespace App\Http\Controllers;

use App\Gateways\InvoiceGateway;
use Illuminate\Http\Request;
use Config;
use App;
use PDF;
use File;

class InvoicesController extends Controller
{
    public function __construct(InvoiceGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->limit = Config::get('dextrader.limit');
    }

    /**
     * Show array of user cards
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $limit = $request->input('limit') ? $request->input('limit') : $this->limit;
        $offset = $request->input('offset') ? $request->input('offset') : 0;
        $response = $this->gateway->findBy('user_id', $id, null, $limit, $offset);
        return response()->ok($response);
    }

    public function download(Request $request)
    {
		$folder = base_path() . '/../public_html/assets/invoices/';
		$invoice = $this->gateway->findBy('id', $request->invoice_id)->first();
		$path = $folder . 'invoice-' . $request->invoice_id . '.pdf';
		if (!File::exists($folder)) {
			File::makeDirectory($folder);
		}
		if (!File::exists($path)) {
			$pdf = PDF::loadView('invoices.details', compact('invoice'));
//        return $pdf->save($path)->stream();
			$pdf->save($path);
		}
      	return response()->ok();
//		return view('invoices.details', ['invoice' => $invoice]);
    }

}
