<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\VendorRequest;
use Illuminate\Pagination\Paginator;

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
     Paginator::useTailwind();
       View::composer('admin.*', function ($view) {
        $pendingCount = VendorRequest::where('status', 'pending')->count();
        $view->with('pendingCount', $pendingCount);
    });
}
}