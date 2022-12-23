<?php

namespace App\Providers;

use App\Http\View\Composers\LeftMenusComposer;
use App\Http\View\Composers\ItemMasterComposer;
use App\Http\View\Composers\NotificationsComposer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


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
        View::composer(
            ['partials.leftmenu'], LeftMenusComposer::class
        );

        View::composer(
            ['partials.notification'], NotificationsComposer::class
        );
    }
}
