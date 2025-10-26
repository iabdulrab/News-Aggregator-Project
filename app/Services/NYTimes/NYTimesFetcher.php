<?php

namespace App\Services\NYTimes;

use App\Interfaces\NewsFetcherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesFetcher implements NewsFetcherInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.nyt_api.base_url', 'https://api.nytimes.com/svc/search/v2');
        $apiKey = config('services.nyt_api.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception(
                'New York Times API key is not configured. Please set NYT_API_KEY in your .env file. ' .
                'Get your free API key at: https://developer.nytimes.com/get-started'
            );
        }
        
        $this->apiKey = $apiKey;
    }

    public function getSourceKey(): string
    {
        return 'nytimes';
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $queryParams = [
                'api-key' => $this->apiKey,
            ];

            // Add optional parameters
            if (!empty($params['q'])) {
                $queryParams['q'] = $params['q'];
            }

            if (!empty($params['fq'])) {
                $queryParams['fq'] = $params['fq'];
            }

            if (!empty($params['from'])) {
                $queryParams['begin_date'] = date('Ymd', strtotime($params['from']));
            }

            if (!empty($params['to'])) {
                $queryParams['end_date'] = date('Ymd', strtotime($params['to']));
            }

            // Add pagination
            $queryParams['page'] = $params['page'] ?? 0;

            // Build the correct URL (avoid double /articlesearch.json)
            $url = $this->baseUrl;
            if (!str_ends_with($url, '/articlesearch.json')) {
                $url .= '/articlesearch.json';
            }
            
            // For local development, you might need to disable SSL verification
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production', // Only verify SSL in production
            ])->timeout(30)->get($url, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                return $data['response']['docs'] ?? [];
            }

            Log::error('NYTimes API fetch failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('NYTimes API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function transformArticle(array $article): array
    {
        // Extract author from byline
        $author = null;
        if (!empty($article['byline']['original'])) {
            $author = str_replace('By ', '', $article['byline']['original']);
        } elseif (!empty($article['byline']['person'])) {
            $person = $article['byline']['person'][0] ?? null;
            if ($person) {
                $author = trim(($person['firstname'] ?? '') . ' ' . ($person['lastname'] ?? ''));
            }
        }

        // Get image URL
        $imageUrl = null;
        if (!empty($article['multimedia'])) {
            $multimedia = $article['multimedia'][0] ?? null;
            if ($multimedia && !empty($multimedia['url'])) {
                $imageUrl = 'https://www.nytimes.com/' . $multimedia['url'];
            }
        }

        // Extract category from section or news_desk
        $category = $article['section_name'] ?? $article['news_desk'] ?? null;

        return [
            'source_article_id' => $article['_id'] ?? null,
            'title' => $article['headline']['main'] ?? '',
            'description' => $article['abstract'] ?? null,
            'content' => $article['lead_paragraph'] ?? null,
            'url' => $article['web_url'] ?? '',
            'url_to_image' => $imageUrl,
            'published_at' => isset($article['pub_date']) ? date('Y-m-d H:i:s', strtotime($article['pub_date'])) : null,
            'author_name' => $author,
            'category' => $category,
            'raw' => $article,
        ];
    }
}

