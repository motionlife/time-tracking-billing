<?php

namespace newlifecfo\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use newlifecfo\Policies\ArrangementPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        //Boot Method Dependency Injection
        //add to fix mysql bug, could be removed when changed to sql server
//        Schema::defaultStringLength(191);
        $this->app->when(ArrangementPolicy::class)
            ->needs('$inAdminMode')
            ->give($request->is('/admin/*') || $request->get('admin'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
