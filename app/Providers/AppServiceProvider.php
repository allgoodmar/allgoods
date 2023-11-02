<?php

namespace App\Providers;

use App\Services\Voyager\Actions\OrderViewAction;
use App\Services\Voyager\Actions\ProductAttributesAction;
use App\Services\Voyager\Actions\ProductGroupSettingsAction;
use App\Services\Voyager\Actions\ReviewablePageAction;
use App\Services\Voyager\Actions\UserApiTokensAction;
use App\Services\Voyager\Actions\UserInstallmentInfoAction;
use App\Services\Voyager\Actions\ViewAction as MyViewAction;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Actions\ViewAction;
use TCG\Voyager\Facades\Voyager;

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
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        Voyager::replaceAction(ViewAction::class, MyViewAction::class);
        Voyager::addAction(OrderViewAction::class);
        Voyager::addAction(UserApiTokensAction::class);
        Voyager::addAction(UserInstallmentInfoAction::class);
        Voyager::addAction(ProductAttributesAction::class);
        Voyager::addAction(ReviewablePageAction::class);
        Voyager::addAction(ProductGroupSettingsAction::class);
    }
}
