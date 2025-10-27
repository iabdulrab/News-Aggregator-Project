<?php

namespace App\Services\Article;

use App\Interfaces\ArticleRepositoryInterface;
use App\Services\NewsAggregatorService;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    protected ArticleRepositoryInterface $articleRepository;
    protected NewsAggregatorService $aggregatorService;

    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        NewsAggregatorService $aggregatorService
    ) {
        $this->articleRepository = $articleRepository;
        $this->aggregatorService = $aggregatorService;
    }

    public function getArticlesWithAutoFetch(array $filters)
    {
        $searchTerm = $filters['search_query'] ?? null;
        $performedAutoFetch = false;

        $articles = $this->articleRepository->getAllWithFilters($filters);
        $totalCount = $this->articleRepository->countByFilters($filters);

        // This is the part where there are no articles in local database then it will fetch from the news sources and save them in database.
        if (!empty($searchTerm) && $totalCount === 0) {
            Log::info('No articles found for search query, fetching from news sources', [
                'search_term' => $searchTerm
            ]);

            try {

                $fetchParams = ['search_query' => $searchTerm];
                if (!empty($filters['from_date'])) {
                    $fetchParams['from_date'] = $filters['from_date'];
                }
                if (!empty($filters['to_date'])) {
                    $fetchParams['to_date'] = $filters['to_date'];
                }
                if (!empty($filters['source_key'])) {
                    $sourceKeys = is_array($filters['source_key']) 
                        ? $filters['source_key'] 
                        : explode(',', $filters['source_key']);
                    
                    $stats = $this->aggregatorService->fetchFromSpecificSources($sourceKeys, $fetchParams);
                } else {
                    $stats = $this->aggregatorService->fetchAndStoreAllArticles($fetchParams);
                }

                Log::info('Auto-fetch completed', [
                    'search_term' => $searchTerm,
                    'fetched' => $stats['total_fetched'],
                    'stored' => $stats['total_stored']
                ]);

                $performedAutoFetch = true;
                $articles = $this->articleRepository->getAllWithFilters($filters);

            } catch (\Exception $e) {
                Log::error('Auto-fetch failed', [
                    'search_term' => $searchTerm,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'articles' => $articles,
            'auto_fetch' => $performedAutoFetch
        ];
    }

    public function getArticleById(int $id)
    {
        return $this->articleRepository->findById($id);
    }

    public function getPersonalizedArticles(array $preferences, int $perPage)
    {
        return $this->articleRepository->getPersonalizedArticles($preferences, $perPage);
    }

    public function getCategories(): array
    {
        return $this->articleRepository->getCategories();
    }

    public function getAuthors(): array
    {
        return $this->articleRepository->getAuthors();
    }
}

