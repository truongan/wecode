<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //Force root URL to allow fixed url when running behind proxy
        // code getting for stack overflow https://stackoverflow.com/questions/35304448/laravel-change-base-url
        \URL::forceRootUrl(\Config::get('app.url'));  
        if (\Str::contains(\Config::get('app.url'), 'https://')) {
            \URL::forceScheme('https');
            //use \URL:forceSchema('https') if you use laravel < 5.4
        }

        //I courtersy of https://github.com/laravel/framework/issues/15361#issuecomment-414023330
        \Illuminate\Pagination\AbstractPaginator::currentPathResolver(function () {
            /** @var \Illuminate\Routing\UrlGenerator $url */
           $url = app('url');
           return $url->current();
        });
    }
}
