<?php

namespace Demo\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->runningInConsole()) {
            $monolog = Log::getMonolog();
            $monolog->popHandler();
            Log::useDailyFiles(storage_path('logs/console.log'));
        }
//        DB::listen(function ($query) {
//            Log::info($query->sql);
//            Log::info($query->bindings);
//        });
//        Log::useDailyFiles(storage_path('logs/day/test.log'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
