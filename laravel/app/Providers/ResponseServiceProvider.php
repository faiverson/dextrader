<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

class ResponseServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot()
	{
		Response::macro('error', function($value, $code = 400) {
			$response = array(
				'success' => false,
				'error' => $value
			);

			return Response($response, $code);
		});

		Response::macro('ok', function($value = null , $code = 200) {
			$response = array(
				'success' => true
			);
			if($value != null) {
				$response['data'] = $value;
			}
			return Response($response, $code);
		});

		Response::macro('added', function($value = null , $code = 201) {
			$response = array(
				'success' => true
			);

			return Response($response, $code);
		});

		Response::macro('pdf', function($pdf, $filename = 'test.pdf') {
			return Response($pdf, 200, [
					'Content-Type' => 'application/pdf',
					'Content-Disposition' =>  'inline; filename="'.$filename.'"'
				]
			);
		});
	}

	public function register()
	{
	}
}
