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
                'NewsAPI key is not configured. Please set NEWSAPI_KEY in your .env file. '
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
                'pageSize' => $params['per_page'] ?? 50,
                'language' => $params['language'] ?? 'en',
            ];

            $useTopHeadlines = !empty($params['article_category']);
            
            if (!empty($params['search_query'])) {
                $queryParams['q'] = $params['search_query'];
            } elseif (!$useTopHeadlines) {
                $queryParams['q'] = 'news OR technology OR business OR sports';
            }

            if (!empty($params['article_category'])) {
                $queryParams['category'] = $params['article_category'];
            }

            if (!empty($params['from_date'])) {
                $queryParams['from'] = date('Y-m-d', strtotime($params['from_date']));
            }

            if (!empty($params['to_date'])) {
                $queryParams['to'] = date('Y-m-d', strtotime($params['to_date']));
            }

            $endpoint = $useTopHeadlines ? '/top-headlines' : '/everything';

            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
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
            'category' => null,
            'raw' => $article,
        ];
    }
}

