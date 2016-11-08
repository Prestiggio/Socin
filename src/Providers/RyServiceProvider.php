<?php

namespace Ry\Socin\Providers;

use Illuminate\Support\ServiceProvider;
use Ry\Socin\App;
use Illuminate\Routing\Router;

class RyServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    	parent::boot();
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
    	
    	$this->map();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->singleton("rysocin", function($app){
    		return $app;
    	});
    	$this->app->singleton("rysocial", function($app){
    		return new App();
    	});
    }
    public function map()
    {
    	/*if (! $this->app->routesAreCached()) {
    		$this->app["router"]->group(['namespace' => 'Ry\Socin\Http\Controllers'], function(){
    			require __DIR__.'/../Http/routes.php';
    		});
    	}*/
    }
    
    public function routes() {
    	app("router")->get("/jostyle/chapapa", "\Ry\Fbform\Http\Controllers\JostyleController@getTest");
    	app("router")->get("/jostyle/tab/edit", "\Ry\Fbform\Http\Controllers\JostyleController@getEdit");
    	app("router")->get("/jostyle/{color}", "\Ry\Fbform\Http\Controllers\JostyleController@getColor")->where("color", "(thanks|submit|green|photos|preview)");
    	app("router")->controller("/jostyle", "\Ry\Fbform\Http\Controllers\JostyleController");
    }
}