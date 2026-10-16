<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(ServerRequestInterface::class, function ($app) {
            $psr17Factory = new Psr17Factory();
            $httpFoundationFactory = new PsrHttpFactory(
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
            );

            return $httpFoundationFactory->createRequest($app->make(Request::class));
        });
    }

   
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('pagination::semantic-ui');
        \Illuminate\Pagination\Paginator::defaultSimpleView('pagination::semantic-ui');
    }
}
