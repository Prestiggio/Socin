<?php

namespace Ry\Socin\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RyServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
    	parent::boot($router);
    	/*
    	$this->publishes([    			
    			__DIR__.'/../config/rysocin.php' => config_path('rysocin.php')
    	], "config");  
    	$this->mergeConfigFrom(
	        	__DIR__.'/../config/rysocin.php', 'rysocin'
	    );
    	$this->publishes([
    			__DIR__.'/../assets' => public_path('vendor/rysocin'),
    	], "public");    	
    	*/
    	//ressources
    	$this->loadViewsFrom(__DIR__.'/../ressources/views', 'rysocin');
    	$this->loadTranslationsFrom(__DIR__.'/../ressources/lang', 'rysocin');
    	/*
    	$this->publishes([
    			__DIR__.'/../ressources/views' => resource_path('views/vendor/rysocin'),
    			__DIR__.'/../ressources/lang' => resource_path('lang/vendor/rysocin'),
    	], "ressources");
    	*/
    	$this->publishes([
    			__DIR__.'/../database/factories/' => database_path('factories'),
	        	__DIR__.'/../database/migrations/' => database_path('migrations')
	    ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
    public function map(Router $router)
    {    	
    	if (! $this->app->routesAreCached()) {
    		$router->group(['namespace' => 'Ry\Socin\Http\Controllers'], function(){
    			require __DIR__.'/../Http/routes.php';
    		});
    	}
    }
}