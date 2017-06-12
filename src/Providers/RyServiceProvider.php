<?php

namespace Ry\Socin\Providers;

use Illuminate\Support\ServiceProvider;
use Ry\Socin\App;
use Ry\Socin\Botengine;
use Illuminate\Routing\Router;
use Ry\Socin\Console\Commands\Colonize;
use Ry\Socin\Console\Commands\Fbparseall;

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
    	
    	$loader = \Illuminate\Foundation\AliasLoader::getInstance();
    	$loader->alias('BotRoute', BotRoute::class);
    	
    	$this->map();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	$this->app->register(\Ry\Md\Providers\RyServiceProvider::class);
    	$this->app->singleton("rysocin", function($app){
    		return $app;
    	});
    	$this->app->singleton("rysocial", function($app){
    		return new App();
    	});
    	$this->app->singleton("rysocin.newapp", function($app){
    		return new Colonize();
    	});
    	$this->app->singleton("rysocin.fbparse", function($app){
    		return new Fbparseall();
    	});
    	$this->app->singleton("rysocin.bot", function($app){
    		return new Botengine();
    	});
    	$this->commands(["rysocin.newapp", "rysocin.fbparse"]);
    }
    public function map()
    {
    	if (! $this->app->routesAreCached()) {
    		$this->app["router"]->group(['namespace' => 'Ry\Socin\Http\Controllers'], function(){
    			require __DIR__.'/../Http/routes.php';
    		});
    	}
    }
}