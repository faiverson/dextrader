<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {

//	protected $defer = true;

	/**
	 *  there is no need to bind classes into the container if they do not depend on any interfaces.
	 * The container does not need to be instructed on how to build these objects,
	 * since it can automatically resolve such "concrete" objects using PHP's reflection services.
	 *
	 */
	public function register()
	{
		// here we can change the UserRepository to use another class with redis ot whatever
		$this->app->bind('App\Repositories\Contracts\UserRepositoryInterface', 'App\Repositories\UserRepository');
		$this->app->bind('App\Repositories\Contracts\CardRepositoryInterface', 'App\Repositories\CardRepository');
		$this->app->bind('App\Repositories\Contracts\HitRepositoryInterface', 'App\Repositories\HitRepository');
//		$this->app->bind('App\Repositories\Contracts\UserGateway',
//			function($app) {
//				return new UserGateway(
//					$app->make('App\Repositories\UserRepository'),
//					new UserCreateValidator( $app['validator'], $app['request'] ),
//					new UserUpdateValidator( $app['validator'], $app['request'] )
//				);
//			});

	}
}