<?php

namespace JincorTech\AuthClient;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use JincorTech\AuthClient\Commands\LoginTenant;
use JincorTech\AuthClient\Commands\RegisterTenant;
use JincorTech\AuthClient\Commands\CreateUser;

/**
 * Class AuthClientServiceProvider
 * @package JincorTech\AuthClient
 */
class AuthClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/jincor-auth.php' => config_path('jincor-auth.php'),
        ]);
    }

    public function register()
    {
        $this->commands([
            RegisterTenant::class,
            LoginTenant::class,
            CreateUser::class,
        ]);

        $this->app->bind(AuthServiceInterface::class, function ($app) {
            return new AuthClient(
                new Client([
                    'base_uri' => config('jincor-auth.uri'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ]
                ])
            );
        });
    }

    public function provides()
    {
        return [
            RegisterTenant::class,
            LoginTenant::class,
            CreateUser::class,
        ];
    }
}
