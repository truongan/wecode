<?php

namespace App\Providers;
use App\Setting;

use Illuminate\Support\ServiceProvider;

class setting_service_provider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        view()->share('settings', Setting::load_all());
    }
}
