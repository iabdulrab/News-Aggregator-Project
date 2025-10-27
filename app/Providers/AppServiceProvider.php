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


        $this->app->singleton('news.fetcher.newsapi', function () {
            return new NewsAPIFetcher();
        });

        $this->app->singleton('news.fetcher.guardian', function () {
            return new GuardianAPIFetcher();
        });

        $this->app->singleton('news.fetcher.nytimes', function () {
            return new NYTimesFetcher();
        });

        $this->app->singleton(NewsAggregatorService::class, function ($app) {
            $fetchers = [];
            
            try {
                $fetchers['newsapi'] = $app->make('news.fetcher.newsapi');
            } catch (\Exception $e) {
            }

            try {
                $fetchers['guardian'] = $app->make('news.fetcher.guardian');
            } catch (\Exception $e) {
            }

            try {
                $fetchers['nytimes'] = $app->make('news.fetcher.nytimes');
            } catch (\Exception $e) {
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
