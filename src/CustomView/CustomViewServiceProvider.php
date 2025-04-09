<?php

namespace Infinitietech\CustomView;

use Illuminate\Support\ServiceProvider;
use Infinitietech\CustomView\Commands\MakeCustomView;

class CustomViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Here you could merge package configuration if needed.
        // $this->mergeConfigFrom(__DIR__.'/../config/customview.php', 'customview');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCustomView::class,
            ]);
        }
    }
}
