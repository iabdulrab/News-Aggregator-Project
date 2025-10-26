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

        // Search by keyword
        if (!empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by date range
        if (!empty($filters['from'])) {
            $query->where('published_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('published_at', '<=', $filters['to']);
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $sourceKeys = is_array($filters['source']) 
                ? $filters['source'] 
                : explode(',', $filters['source']);
            
            $query->whereHas('source', function ($q) use ($sourceKeys) {
                $q->whereIn('key', $sourceKeys);
            });
        }

        // Filter by author
        if (!empty($filters['author'])) {
            $query->where('author_name', 'like', '%' . $filters['author'] . '%');
        }

        // Sort by date
        $sortOrder = $filters['sort'] ?? 'desc';
        $query->orderBy('published_at', $sortOrder);

        // Paginate
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

        if (!empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['from'])) {
            $query->where('published_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('published_at', '<=', $filters['to']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['source'])) {
            $sourceKeys = is_array($filters['source']) 
                ? $filters['source'] 
                : explode(',', $filters['source']);
            
            $query->whereHas('source', function ($q) use ($sourceKeys) {
                $q->whereIn('key', $sourceKeys);
            });
        }

        if (!empty($filters['author'])) {
            $query->where('author_name', 'like', '%' . $filters['author'] . '%');
        }

        return $query->count();
    }
}

