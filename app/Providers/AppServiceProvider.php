<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      \App\Models\User::observe(\App\Observers\UserObserver::class);
      \App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
      \App\Models\Topic::observe(\App\Observers\TopicObserver::class);
      \App\Models\Link::observe(\App\Observers\LinkObserver::class);

        //
      \Carbon\Carbon::setLocale('zh');
      Horizon::auth(function ($request) {
            // return true / false;
          return true;
      });
  }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // if (app()->isLocal()) {
        //     $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        // }
       if (config('app.debug'))
       {
        $this->app->register('VIACreative\SudoSu\ServiceProvider');
    }
}
}
