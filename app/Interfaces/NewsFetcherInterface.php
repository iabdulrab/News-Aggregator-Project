<?php

namespace App\Interfaces;

interface NewsFetcherInterface
{
    /**
     * Fetch articles from the news source
     *
     * @param array $params Query parameters (search, category, from, to, etc.)
     * @return array Array of articles
     */
    public function fetchArticles(array $params = []): array;

    /**
     * Get the source identifier
     *
     * @return string
     */
    public function getSourceKey(): string;

    /**
     * Transform raw article data to standard format
     *
     * @param array $article
     * @return array
     */
    public function transformArticle(array $article): array;
}
