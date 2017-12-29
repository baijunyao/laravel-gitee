<?php

namespace Baijunyao\LaravelGitee;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class GiteeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
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
