<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Config;

class RouteServiceProvider extends ServiceProvider
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
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router, Request $request)
    {
//		echo $request->path();
//		echo '<br>'.$request->url();exit;
		$this->loadRoutesFrom(app_path('Http/Routes/routes.php'));

		if ($request->is('abo*') || $request->is('abo/*')) {
			$this->loadRoutesFrom(app_path('Http/Routes/admin.php'));
		}

		if ($request->is('secure*') || $request->is('secure/*')) {
			$this->loadRoutesFrom(app_path('Http/Routes/sales.php'));
		}

		$router->group(['prefix' => 'api'], function () use($request) {
			if ($request->is('api/users*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/users.php'));
			}

			if ($request->is('api/roles*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/roles.php'));
			}

			if ($request->is('api/marketing-links*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/marketing_links.php'));
			}

			if ($request->is('api/providers*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/providers.php'));
			}

			if ($request->is('api/files*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/files.php'));
			}

			if ($request->is('api/training*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/trainings.php'));
			}

			if ($request->is('api/checkout*') || $request->is('api/transactions*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/checkout.php'));
			}

			if ($request->is('api/hits*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/hits.php'));
			}

			if ($request->is('api/countries*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/countries.php'));
			}

			if ($request->is('api/testimonials*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/testimonials.php'));
			}

			if ($request->is('api/purchases*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/purchases.php'));
			}

			if ($request->is('api/invoices*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/invoices.php'));
			}

			if ($request->is('api/subscriptions*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/subscriptions.php'));
			}

			if ($request->is('api/signals*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/signals.php'));
			}

			if ($request->is('api/offers*')) {
				$this->loadRoutesFrom(app_path('Http/Routes/offers.php'));
			}
		});
    }
}
