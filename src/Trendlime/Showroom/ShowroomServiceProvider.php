<?php

namespace Trendlime\Showroom;

use Illuminate\Support\ServiceProvider;
use Trendlime\Showroom\Services\Showroom;

/**
 * This file is part of Showroom,
 * a picture management solution for Laravel.
 *
 * @license Apache
 * @package Trendlime\Showroom
 */
class ShowroomServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes(
            [
                real_path(__DIR__ . '/Config') . '/showroom.php' => config_path('showroom.php')
            ],
            'config'
        );

        $this->publishes(
            [
                real_path(__DIR__) . '/Migrations' => $this->app->databasePath() . '/migrations',
            ],
            'migrations'
        );

        /*
        $this->publishes(
            [
                realpath(__DIR__ . '/../../resources') . '/assets' => public_path('vendor/gallery'),
            ],
            'assets'
        );
        */
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            realpath(__DIR__ . '/Config') . '/showroom.php',
            'showroom'
        );

        $this->registerShowroom();
    }


    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerShowroom()
    {
        $this->app->bindShared('showroom', function ($app) {
            return new Showroom($app);
        });

        $this->app->alias('showroom', Showroom::class);
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'showroom',
            Showroom::class
        ];
    }

}