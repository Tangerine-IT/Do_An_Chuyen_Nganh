<?php

namespace App\Providers;

use App\Contracts\AiClient;
use App\Contracts\CouponService as CouponServiceContract;
use App\Contracts\PaymentGateway;
use App\Services\CouponService;
use App\Services\HttpAiClient;
use App\Services\RuleBasedAiClient;
use App\Services\VnpayGateway;
use App\View\Composers\SiteNavigationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CouponServiceContract::class, CouponService::class);
        $this->app->bind(AiClient::class, fn () => config('services.ai.provider') === 'http'
            ? app(HttpAiClient::class)
            : app(RuleBasedAiClient::class));
        $this->app->bind(PaymentGateway::class, VnpayGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', SiteNavigationComposer::class);
    }
}
