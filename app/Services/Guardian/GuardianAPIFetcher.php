<?php

namespace App\Services\Guardian;

use App\Interfaces\NewsFetcherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianAPIFetcher implements NewsFetcherInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.guardian_api.base_url', 'https://content.guardianapis.com');
        $apiKey = config('services.guardian_api.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception(
                'Guardian API key is not configured. Please set GUARDIAN_API_KEY in your .env file. '
            );
        }
        
        $this->apiKey = $apiKey;
    }

    public function getSourceKey(): string
    {
        return 'guardian';
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $queryParams = [
                'api-key' => $this->apiKey,
                'page-size' => $params['per_page'] ?? 50,
                'show-tags' => 'contributor',
                'order-by' => 'newest',
            ];

            if (!empty($params['search_query'])) {
                $queryParams['q'] = $params['search_query'];
            }

            if (!empty($params['article_category'])) {
                $queryParams['section'] = $params['article_category'];
            }

            if (!empty($params['from_date'])) {
                $queryParams['from-date'] = date('Y-m-d', strtotime($params['from_date']));
            }

            if (!empty($params['to_date'])) {
                $queryParams['to-date'] = date('Y-m-d', strtotime($params['to_date']));
            }

            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
            ])->timeout(30)->get($this->baseUrl . '/search', $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['response']['results'] ?? [];
                
                Log::info('Guardian API fetch successful', [
                    'total' => $data['response']['total'] ?? 0,
                    'fetched' => count($results)
                ]);
                
                return $results;
            }

            Log::error('Guardian API fetch failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Guardian API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function transformArticle(array $article): array
    {
        $fields = $article['fields'] ?? [];
        $tags = $article['tags'] ?? [];
        
        $author = null;
        if (!empty($tags)) {
            $contributorTags = array_filter($tags, fn($tag) => ($tag['type'] ?? '') === 'contributor');
            if (!empty($contributorTags)) {
                $author = reset($contributorTags)['webTitle'] ?? null;
            }
        }
        if (!$author && !empty($fields['byline'])) {
            $author = $fields['byline'];
        }

        $transformed = [
            'source_article_id' => $article['id'] ?? null,
            'title' => $article['webTitle'] ?? '',
            'description' => $fields['trailText'] ?? null,
            'content' => $fields['body'] ?? null,
            'url' => $article['webUrl'] ?? '',
            'url_to_image' => $fields['thumbnail'] ?? null,
            'published_at' => isset($article['webPublicationDate']) ? date('Y-m-d H:i:s', strtotime($article['webPublicationDate'])) : null,
            'author_name' => $author,
            'category' => $article['sectionName'] ?? null,
            'raw' => $article,
        ];

        return $transformed;
    }
}

