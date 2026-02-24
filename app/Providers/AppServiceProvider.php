<?php

namespace App\Providers;

use App\Services\PostGenerationService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PostGenerationService::class, function ($app) {
            return new PostGenerationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(RateLimiter $rateLimiter): void
    {
        $rateLimiter->for('user-login', function (Request $request) {
            $key = 'user-login:' . $request->ip();
            return Limit::perMinute(5)
                ->by($key)->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again in a minute or two',
                    ], 429);
                });
        });

        $rateLimiter->for('user-registration', function (Request $request) {
            $key = 'user-registration:' . $request->ip();
            return Limit::perMinute(5)
                ->by($key)->response(function () {
                    return response()->json([
                        'message' => 'Too many attempts to register. Please try again later.',
                    ], 429);
                });
        });

        $rateLimiter->for('post-generation', function (Request $request) {
            $key = 'post-generation:' . $request->user()->id;
            $perMinute = 5;
            return Limit::perMinute($perMinute)
                ->by($key)->response(function () use ($perMinute) {
                    return response()->json([
                        'message' => 'Too many requests. Only ' . $perMinute . ' requests allowed per minute.',
                    ], 429);
                });
        });
    }
}
