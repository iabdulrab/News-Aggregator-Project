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
                'New York Times API key is not configured. Please set NYT_API_KEY in your .env file. '
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

            if (!empty($params['search_query'])) {
                $queryParams['q'] = $params['search_query'];
            }

            if (!empty($params['article_category'])) {
                $queryParams['fq'] = $params['article_category'];
            }

            if (!empty($params['from_date'])) {
                $queryParams['begin_date'] = date('Ymd', strtotime($params['from_date']));
            }

            if (!empty($params['to_date'])) {
                $queryParams['end_date'] = date('Ymd', strtotime($params['to_date']));
            }

            $queryParams['page'] = $params['page'] ?? 0;

            $url = $this->baseUrl;
            if (!str_ends_with($url, '/articlesearch.json')) {
                $url .= '/articlesearch.json';
            }
            
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
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
        $author = null;
        if (!empty($article['byline']['original'])) {
            $author = str_replace('By ', '', $article['byline']['original']);
        } elseif (!empty($article['byline']['person'])) {
            $person = $article['byline']['person'][0] ?? null;
            if ($person) {
                $author = trim(($person['firstname'] ?? '') . ' ' . ($person['lastname'] ?? ''));
            }
        }

        $imageUrl = null;
        if (!empty($article['multimedia'])) {
            $multimedia = $article['multimedia'][0] ?? null;
            if ($multimedia && !empty($multimedia['url'])) {
                $imageUrl = 'https://www.nytimes.com/' . $multimedia['url'];
            }
        }

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

