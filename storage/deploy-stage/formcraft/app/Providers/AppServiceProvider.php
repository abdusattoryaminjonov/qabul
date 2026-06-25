<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->environment('local') && ($rootUrl = config('app.url'))) {
            URL::forceRootUrl($rootUrl);
        }

        if (! $this->app->runningInConsole()) {
            $host = request()->getHost();

            if (str_ends_with($host, 'jprq.live') || str_ends_with($host, 'jprq.io')) {
                URL::forceScheme('https');
                URL::forceRootUrl('https://'.$host);
            }
        }

        \Illuminate\Validation\Rules\Password::defaults(fn () => \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers());

        View::composer('admin.layout', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'headerNotifications' => auth()->user()->unreadNotifications()->latest()->take(6)->get(),
                    'headerNotificationCount' => auth()->user()->unreadNotifications()->count(),
                ]);
            }
        });
    }
}
