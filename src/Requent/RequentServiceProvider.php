<?php

namespace Requent;

use Illuminate\Support\ServiceProvider;
use Requent\UrlParser\QueryStringParser as Parser;

class RequentServiceProvider extends ServiceProvider
{
	public function register()
	{
		// ...
	}

	public function boot()
	{
		$requent = $this->makeRequentInstance();

		$this->app->instance('requent', $requent);
		
		$this->app->instance(Requent::class, $requent);

		$this->mergeConfigFrom( __DIR__.'/Config/requent.php', 'requent');

		$this->publishes([
	        __DIR__.'/Config/requent.php' => config_path('requent.php'),
	    ], 'config');
	}

	protected function makeRequentInstance()
	{
		$config = $this->app->config->get('requent');
		$key = $config['query_identifier'];
		$query = $this->app->request->query();
		$parsedArray = Parser::parse(
			isset($query[$key]) ? $query[$key] : '', $key
		);
		return new Requent($config, $query, $parsedArray);
	}
}