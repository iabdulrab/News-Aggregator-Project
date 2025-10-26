<?php

namespace App\Providers;

use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\NewsFetcherInterface;
use App\Interfaces\SourceRepositoryInterface;
use App\Interfaces\UserPreferenceRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\SourceRepository;
use App\Repositories\UserPreferenceRepository;
use App\Repositories\UserRepository;
use App\Services\Guardian\GuardianAPIFetcher;
use App\Services\NewsAggregatorService;
use App\Services\NewsAPI\NewsAPIFetcher;
use App\Services\NYTimes\NYTimesFetcher;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding Interfaces to Repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(SourceRepositoryInterface::class, SourceRepository::class);
        $this->app->bind(UserPreferenceRepositoryInterface::class, UserPreferenceRepository::class);


        // Register individual news fetchers (lazy loading)
        $this->app->singleton('news.fetcher.newsapi', function () {
            return new NewsAPIFetcher();
        });

        $this->app->singleton('news.fetcher.guardian', function () {
            return new GuardianAPIFetcher();
        });

        $this->app->singleton('news.fetcher.nytimes', function () {
            return new NYTimesFetcher();
        });

        // Register the main News Aggregator Service
        $this->app->singleton(NewsAggregatorService::class, function ($app) {
            $fetchers = [];
            
            // Only instantiate fetchers that are properly configured
            try {
                $fetchers['newsapi'] = $app->make('news.fetcher.newsapi');
            } catch (\Exception $e) {
                // NewsAPI not configured, skip it
            }

            try {
                $fetchers['guardian'] = $app->make('news.fetcher.guardian');
            } catch (\Exception $e) {
                // Guardian not configured, skip it
            }

            try {
                $fetchers['nytimes'] = $app->make('news.fetcher.nytimes');
            } catch (\Exception $e) {
                // NYTimes not configured, skip it
            }

            return new NewsAggregatorService($fetchers);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
