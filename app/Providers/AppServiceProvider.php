<?php

namespace App\Providers;

use App\Satchel;
use App\Compose;
use App\Laradock;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
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
        $this->app->singleton(Laradock::class, function (Application $app) {
            return new Laradock(
                new Client(['base_uri' => 'https://api.github.com/']),
                config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . 'laradock.zip',
                $app->make(Satchel::class)
            );
        });

        // Bind Compose.
        $this->app->bind(Compose::class, function (Application $app) {
            return new Compose(
                Storage::disk('work_dir'),
                $app->make(Laradock::class),
                'docker-compose.yml'
            );
        });
    }
}
