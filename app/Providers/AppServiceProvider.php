<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(): void
    {
        \URL::forceRootUrl(config('app.url'));
        \URL::forceScheme('http');
    }

}
