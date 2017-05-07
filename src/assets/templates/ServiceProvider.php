Dans boot
---------
app("kto")->setupMiddleware();

------------------------------
Dans register
------------------------------
$this->app->singleton("kto", function($app){
	return new BaseSocial(
			"kto",
			"rykto",
			[
					"base" => "/kto",
					"controller" => PublicController::class,
					"ajax" => ["kto"]
			],
			[
					"facebook" => [
							'app_id' => "APP_ID sur facebook",
							'app_secret' => "APP_SECRET sur facebook",
							'default_graph_version' => 'VERSION sur facebook',
					]],
			Kto::class
	);
});