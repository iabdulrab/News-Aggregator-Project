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
        $searchTerm = $filters['q'] ?? null;
        $performedAutoFetch = false;

        // Get articles with filters
        $articles = $this->articleRepository->getAllWithFilters($filters);
        $totalCount = $this->articleRepository->countByFilters($filters);

        // AUTO-FETCH FEATURE: If search query provided but no results found, fetch from news sources
        if (!empty($searchTerm) && $totalCount === 0) {
            Log::info('No articles found for search query, fetching from news sources', [
                'search_term' => $searchTerm
            ]);

            try {
                // Prepare parameters for fetching
                $fetchParams = ['q' => $searchTerm];

                // Add date filters if provided
                if (!empty($filters['from'])) {
                    $fetchParams['from'] = $filters['from'];
                }
                if (!empty($filters['to'])) {
                    $fetchParams['to'] = $filters['to'];
                }

                // Fetch from all news sources or specific sources if filter applied
                if (!empty($filters['source'])) {
                    $sourceKeys = is_array($filters['source']) 
                        ? $filters['source'] 
                        : explode(',', $filters['source']);
                    
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

                // Rebuild query to get the newly fetched articles
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

