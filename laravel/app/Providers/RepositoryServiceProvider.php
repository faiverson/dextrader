<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {

	public function register()
	{
		// here we can change the UserRepository to use another class with redis ot whatever
		$this->app->bind('App\Repositories\Contracts\UserRepositoryInterface', 'App\Repositories\UserRepository');
		$this->app->bind('App\Repositories\Contracts\UserGateway',
			function($app) {
				return new UserGateway(
					$app->make('App\Repositories\UserRepository'),
					new UserCreateValidator( $app['validator'], $app['request'] ),
					new UserUpdateValidator( $app['validator'], $app['request'] )
				);
			});
	}
}