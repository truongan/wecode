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
        try{
            view()->share('settings', Setting::load_all());
        } catch(  \Illuminate\Database\QueryException $e){
            var_dump('fail to load settings: ' . $e->getMessage());
            // Log::critical('fail to load settings: ' . $e->getMessage());
        }
        
    }
}
