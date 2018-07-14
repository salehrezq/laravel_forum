<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        $channeles = Cache::rememberForever('channels', function () {
                    return \App\Channel::all();
                });

        view()->share('channels', $channeles);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
