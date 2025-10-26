<?php

namespace App\Services\NewsAPI;

use App\Interfaces\NewsFetcherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsAPIFetcher implements NewsFetcherInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.newsapi.base_url', 'https://newsapi.org/v2');
        $apiKey = config('services.newsapi.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception(
                'NewsAPI key is not configured. Please set NEWSAPI_KEY in your .env file. ' .
                'Get your free API key at: https://newsapi.org/register'
            );
        }
        
        $this->apiKey = $apiKey;
    }

    public function getSourceKey(): string
    {
        return 'newsapi';
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $queryParams = [
                'apiKey' => $this->apiKey,
                'pageSize' => $params['pageSize'] ?? 100,
                'language' => $params['language'] ?? 'en',
            ];

            // Determine which endpoint to use
            $useTopHeadlines = !empty($params['category']);
            
            // Add optional parameters
            if (!empty($params['q'])) {
                $queryParams['q'] = $params['q'];
            } elseif (!$useTopHeadlines) {
                // NewsAPI /everything endpoint REQUIRES at least one of: q, qInTitle, sources, or domains
                // Default to fetching general news articles
                $queryParams['q'] = 'news OR technology OR business OR sports';
            }

            if (!empty($params['category'])) {
                $queryParams['category'] = $params['category'];
            }

            if (!empty($params['from'])) {
                $queryParams['from'] = $params['from'];
            }

            if (!empty($params['to'])) {
                $queryParams['to'] = $params['to'];
            }

            // Use 'everything' endpoint for comprehensive search, 'top-headlines' for categories
            $endpoint = $useTopHeadlines ? '/top-headlines' : '/everything';

            // For local development, you might need to disable SSL verification
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production', // Only verify SSL in production
            ])->timeout(30)->get($this->baseUrl . $endpoint, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                $articles = $data['articles'] ?? [];
                
                Log::info('NewsAPI fetch successful', [
                    'total' => $data['totalResults'] ?? 0,
                    'fetched' => count($articles),
                    'endpoint' => $endpoint
                ]);
                
                return $articles;
            }

            Log::error('NewsAPI fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('NewsAPI exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function transformArticle(array $article): array
    {
        return [
            'source_article_id' => $article['url'] ?? null,
            'title' => $article['title'] ?? '',
            'description' => $article['description'] ?? null,
            'content' => $article['content'] ?? null,
            'url' => $article['url'] ?? '',
            'url_to_image' => $article['urlToImage'] ?? null,
            'published_at' => isset($article['publishedAt']) ? date('Y-m-d H:i:s', strtotime($article['publishedAt'])) : null,
            'author_name' => $article['author'] ?? null,
            'category' => null, // NewsAPI doesn't always provide category in response
            'raw' => $article,
        ];
    }
}

