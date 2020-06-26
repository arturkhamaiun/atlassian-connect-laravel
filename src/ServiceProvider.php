<?php

namespace AtlassianConnectLaravel;

use AtlassianConnectLaravel\Api\ApiClient;
use AtlassianConnectLaravel\Auth\JwtGuard;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any package services.
     */
    public function register()
    {
        Auth::extend('jwt', function (Application $app, $name, array $config) {
            return $app->makeWith(JwtGuard::class, [
                'provider' => Auth::createUserProvider($config['provider']),
            ]);
        });

        $this->app->bind(ApiClient::class, function () {
            return ApiClient::create(Auth::user(), config('plugin.apiVersion'));
        });
    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $root = __DIR__.'/..';

        $this->loadRoutesFrom("{$root}/routes/plugin.php");
        Route::getRoutes()->refreshNameLookups();

        $this->mergeConfigFrom("{$root}/config/descriptor.php", 'descriptor');
        $this->publishes(["{$root}/config/descriptor.php" => config_path('descriptor.php')], 'atlassian-connect-laravel');

        $this->mergeConfigFrom("{$root}/config/plugin.php", 'plugin');
        $this->publishes(["{$root}/config/plugin.php" => config_path('plugin.php')], 'atlassian-connect-laravel');

        $this->mergeRecursiveConfigFrom("{$root}/config/auth.php", 'auth');

        $this->loadMigrationsFrom("{$root}/database/migrations");
    }

    protected function mergeRecursiveConfigFrom($path, $key)
    {
        if (!($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $this->app['config']->set($key, array_merge_recursive(
                require $path,
                $this->app['config']->get($key, [])
            ));
        }
    }
}
