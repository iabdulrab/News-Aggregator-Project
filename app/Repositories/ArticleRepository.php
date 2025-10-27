<?php

namespace App\Repositories;

use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getAllWithFilters(array $filters): LengthAwarePaginator
    {
        $query = Article::with('source');

        if (!empty($filters['search_query'])) {
            $searchTerm = $filters['search_query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['from_date'])) {
            $query->where('published_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('published_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['article_category'])) {
            $query->where('category', $filters['article_category']);
        }

        if (!empty($filters['source_key'])) {
            $sourceKeys = is_array($filters['source_key']) 
                ? $filters['source_key'] 
                : explode(',', $filters['source_key']);
            
            $query->whereHas('source', function ($q) use ($sourceKeys) {
                $q->whereIn('key', $sourceKeys);
            });
        }

        if (!empty($filters['author_name'])) {
            $query->where('author_name', 'like', '%' . $filters['author_name'] . '%');
        }

        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy('published_at', $sortOrder);

        $perPage = min($filters['per_page'] ?? 15, 100);
        
        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Article::with('source')->find($id);
    }

    public function getCategories(): array
    {
        return Article::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();
    }

    public function getAuthors(): array
    {
        return Article::whereNotNull('author_name')
            ->distinct()
            ->pluck('author_name')
            ->filter()
            ->values()
            ->toArray();
    }

    public function getPersonalizedArticles(array $preferences, int $perPage): LengthAwarePaginator
    {
        $query = Article::with('source');

        // Filter by preferred sources
        if (!empty($preferences['sources'])) {
            $query->whereHas('source', function ($q) use ($preferences) {
                $q->whereIn('name', $preferences['sources']);
            });
        }

        // Filter by preferred categories
        if (!empty($preferences['categories'])) {
            $query->whereIn('category', $preferences['categories']);
        }

        // Filter by preferred authors
        if (!empty($preferences['authors'])) {
            $query->where(function ($q) use ($preferences) {
                foreach ($preferences['authors'] as $author) {
                    $q->orWhere('author_name', 'like', "%{$author}%");
                }
            });
        }

        // Sort by date (newest first)
        $query->orderBy('published_at', 'desc');

        return $query->paginate($perPage);
    }

    public function countByFilters(array $filters): int
    {
        $query = Article::query();

        if (!empty($filters['search_query'])) {
            $searchTerm = $filters['search_query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['from_date'])) {
            $query->where('published_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('published_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['article_category'])) {
            $query->where('category', $filters['article_category']);
        }

        if (!empty($filters['source_key'])) {
            $sourceKeys = is_array($filters['source_key']) 
                ? $filters['source_key'] 
                : explode(',', $filters['source_key']);
            
            $query->whereHas('source', function ($q) use ($sourceKeys) {
                $q->whereIn('key', $sourceKeys);
            });
        }

        if (!empty($filters['author_name'])) {
            $query->where('author_name', 'like', '%' . $filters['author_name'] . '%');
        }

        return $query->count();
    }
}

