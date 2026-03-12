<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use App\Services\CartService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->validateEnvironment();
        $this->configureRateLimiting();
        $this->registerObservers();
        $this->shareCartCount();
    }

    /**
     * Share cart count with all views.
     */
    protected function shareCartCount(): void
    {
        View::composer(['layouts.web', 'web.*'], function ($view) {
            $cartCount = 0;
            $menuCategories = Cache::remember('menu_categories_top_12', now()->addMinutes(15), function () {
                return Category::query()
                    ->active()
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(12)
                    ->get(['id', 'name', 'slug']);
            });

            if (auth()->check()) {
                $cartService = app(CartService::class);
                $cartCount = $cartService->getCartCount(auth()->id(), null);
            }
            else {
                $cartService = app(CartService::class);
                $cartCount = $cartService->getCartCount(null, session()->getId());
            }

            $view->with('cartCount', $cartCount);
            $view->with('menuCategories', $menuCategories);
        });
    }

    /**
     * Validate required environment variables.
     */
    protected function validateEnvironment(): void
    {
        $requiredVars = [
            'app.key' => 'Application key is required. Run php artisan key:generate',
            'database.connections.mysql.host' => 'Database host is required',
            'database.connections.mysql.database' => 'Database name is required',
        ];

        $missing = [];

        foreach ($requiredVars as $var => $message) {
            if (empty(config($var))) {
                $missing[] = $message;
            }
        }

        if (!empty($missing) && !$this->app->runningInConsole()) {
            if ($this->app->environment('production')) {
                throw new \RuntimeException(implode('. ', $missing));
            }
        }

        if (app()->environment('production')) {
            Validator::extend('secure_url', function ($attribute, $value, $parameters, $validator) {
                return str_starts_with($value, 'https://');
            });

            Validator::replacer('secure_url', function ($message, $attribute, $rule, $parameters) {
                return 'The :attribute must be a secure HTTPS URL in production.';
            });
        }
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for ('login', function (Request $request) {
            $email = (string)$request->email;

            return Limit::perMinute(5)->by($email . $request->ip())->response(function () {
                    return back()->withErrors([
                    'email' => __('Too many login attempts. Please try again in one minute.'),
                    ])->onlyInput('email');
                }
                );
            });

        RateLimiter::for ('checkout', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                    return back()->withErrors([
                    'checkout' => __('Too many checkout attempts. Please try again in a minute.'),
                    ]);
                }
                );
            });
    }

    protected function registerObservers(): void
    {
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
    }
}
