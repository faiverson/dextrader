<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use DateTime;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//		Item::created(function ($item) {
//			Event::fire(new ItemCreated($item));
//		});
		Blade::directive('dates', function($expression) {
			$output = "<?php echo (new DateTime{$expression})->format('l jS F Y'); ?>";
			return $output;
		});
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
