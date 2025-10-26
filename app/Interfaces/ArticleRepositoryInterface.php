<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function getAllWithFilters(array $filters): LengthAwarePaginator;
    
    public function findById(int $id);
    
    public function getCategories(): array;
    
    public function getAuthors(): array;
    
    public function getPersonalizedArticles(array $preferences, int $perPage): LengthAwarePaginator;
    
    public function countByFilters(array $filters): int;
}

