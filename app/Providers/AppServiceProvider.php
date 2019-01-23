<?php

namespace App\Providers;

use App\Laradock;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Laradock.
        $this->app->singleton(Laradock::class, function ($app) {
            return new Laradock(
                new Client(['base_uri' => 'https://api.github.com/']),
                config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . 'laradock.zip'
            );
        });
    }
}
