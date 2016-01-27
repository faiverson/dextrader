<?php

namespace App\Providers;

use App\Models\IBSignal;
use Illuminate\Support\ServiceProvider;

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
