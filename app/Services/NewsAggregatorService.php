<?php

namespace App\Services;

use App\Interfaces\NewsFetcherInterface;
use App\Models\Article;
use App\Models\Source;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    protected array $fetchers;

    public function __construct(array $fetchers = [])
    {
        $this->fetchers = $fetchers;
    }

    /**
     * Register a news fetcher
     *
     * @param NewsFetcherInterface $fetcher
     * @return void
     */
    public function registerFetcher(NewsFetcherInterface $fetcher): void
    {
        $this->fetchers[$fetcher->getSourceKey()] = $fetcher;
    }

    /**
     * Fetch and store articles from all sources
     *
     * @param array $params
     * @return array Statistics about fetched articles
     */
    public function fetchAndStoreAllArticles(array $params = []): array
    {
        $stats = [
            'total_fetched' => 0,
            'total_stored' => 0,
            'sources' => [],
        ];

        foreach ($this->fetchers as $sourceKey => $fetcher) {
            try {
                $result = $this->fetchAndStoreFromSource($fetcher, $params);
                $stats['sources'][$sourceKey] = $result;
                $stats['total_fetched'] += $result['fetched'];
                $stats['total_stored'] += $result['stored'];
            } catch (\Exception $e) {
                Log::error("Failed to fetch from {$sourceKey}", [
                    'error' => $e->getMessage()
                ]);
                $stats['sources'][$sourceKey] = [
                    'fetched' => 0,
                    'stored' => 0,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $stats;
    }

    /**
     * Fetch and store articles from a specific source
     *
     * @param NewsFetcherInterface $fetcher
     * @param array $params
     * @return array
     */
    public function fetchAndStoreFromSource(NewsFetcherInterface $fetcher, array $params = []): array
    {
        $sourceKey = $fetcher->getSourceKey();
        
        // Get or create source
        $source = Source::firstOrCreate(
            ['key' => $sourceKey],
            ['name' => ucfirst($sourceKey)]
        );

        // Fetch articles
        $articles = $fetcher->fetchArticles($params);
        $fetched = count($articles);
        $stored = 0;

        foreach ($articles as $articleData) {
            try {
                $transformed = $fetcher->transformArticle($articleData);
                
                // Skip if URL is missing or invalid
                if (empty($transformed['url']) || empty($transformed['title'])) {
                    continue;
                }

                // Store article (updateOrCreate to avoid duplicates)
                Article::updateOrCreate(
                    ['url' => $transformed['url']],
                    array_merge($transformed, ['source_id' => $source->id])
                );

                $stored++;
            } catch (\Exception $e) {
                Log::warning("Failed to store article from {$sourceKey}", [
                    'error' => $e->getMessage(),
                    'article' => $articleData['title'] ?? 'Unknown'
                ]);
            }
        }

        return [
            'fetched' => $fetched,
            'stored' => $stored,
        ];
    }

    /**
     * Fetch articles from specific sources only
     *
     * @param array $sourceKeys
     * @param array $params
     * @return array
     */
    public function fetchFromSpecificSources(array $sourceKeys, array $params = []): array
    {
        $stats = [
            'total_fetched' => 0,
            'total_stored' => 0,
            'sources' => [],
        ];

        foreach ($sourceKeys as $sourceKey) {
            if (isset($this->fetchers[$sourceKey])) {
                $fetcher = $this->fetchers[$sourceKey];
                $result = $this->fetchAndStoreFromSource($fetcher, $params);
                $stats['sources'][$sourceKey] = $result;
                $stats['total_fetched'] += $result['fetched'];
                $stats['total_stored'] += $result['stored'];
            }
        }

        return $stats;
    }

    /**
     * Get all registered fetchers
     *
     * @return array
     */
    public function getFetchers(): array
    {
        return $this->fetchers;
    }
}

