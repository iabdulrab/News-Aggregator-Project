<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Article\FetchArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Services\Article\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get all articles with filtering and search",
     *     description="Retrieve paginated list of articles with advanced filtering. Features auto-fetch: if search query returns no results, automatically fetches from news sources (NewsAPI, Guardian, NYTimes)",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="search_query",
     *         in="query",
     *         description="Search keyword (searches in title, description, content). Triggers auto-fetch if no results found.",
     *         required=false,
     *         @OA\Schema(type="string", example="cryptocurrency")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter articles from this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-01")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter articles until this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-10-25")
     *     ),
     *     @OA\Parameter(
     *         name="article_category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string", example="Technology")
     *     ),
     *     @OA\Parameter(
     *         name="source_key",
     *         in="query",
     *         description="Filter by source(s). Comma-separated for multiple sources.",
     *         required=false,
     *         @OA\Schema(type="string", example="newsapi,guardian,nytimes")
     *     ),
     *     @OA\Parameter(
     *         name="author_name",
     *         in="query",
     *         description="Filter by author name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string", example="John")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order by published date",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, maximum=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Breaking News Title"),
     *                     @OA\Property(property="description", type="string", example="Article description"),
     *                     @OA\Property(property="url", type="string", example="https://example.com/article"),
     *                     @OA\Property(property="url_to_image", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="published_at", type="string", format="date-time", example="2025-10-25T10:30:00.000000Z"),
     *                     @OA\Property(property="author_name", type="string", example="Jane Smith"),
     *                     @OA\Property(property="category", type="string", example="Technology"),
     *                     @OA\Property(property="source", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="key", type="string", example="newsapi"),
     *                         @OA\Property(property="name", type="string", example="NewsAPI")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=150),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="last_page", type="integer", example=10)
     *         )
     *     )
     * )
     */
    public function index(FetchArticleRequest $request): JsonResponse
    {
        $filters = $request->filters();
        $result = $this->articleService->getArticlesWithAutoFetch($filters);
        $message = $result['auto_fetch'] ? 'Articles retrieved successfully (auto-fetched from news sources)' : 'Articles retrieved successfully';
        return $this->paginatedResponse(ArticleResource::collection($result['articles']), $message);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a single article by ID",
     *     description="Retrieve detailed information about a specific article",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Breaking News Title"),
     *             @OA\Property(property="description", type="string", example="Article description"),
     *             @OA\Property(property="content", type="string", example="Full article content..."),
     *             @OA\Property(property="url", type="string", example="https://example.com/article"),
     *             @OA\Property(property="published_at", type="string", format="date-time"),
     *             @OA\Property(property="source", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="key", type="string"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article not found")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->articleService->getArticleById($id);
        if (!$article) {
            return $this->notFoundResponse('Article not found');
        }
        return $this->successResponse(new ArticleResource($article), 'Article retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/articles/personalized/feed",
     *     summary="Get personalized article feed",
     *     description="Retrieve articles filtered by user's saved preferences (sources, categories, authors). Requires authentication.",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authentication required")
     *         )
     *     )
     * )
     */
    public function personalized(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorizedResponse('Authentication required');
        }

        $preferences = $user->preference?->preferences ?? [];
        $perPage = min($request->input('per_page', 15), 100);

        $articles = $this->articleService->getPersonalizedArticles($preferences, $perPage);

        return $this->paginatedResponse($articles, 'Personalized articles retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/articles/meta/categories",
     *     summary="Get all available categories",
     *     description="Retrieve a list of all unique categories from stored articles",
     *     tags={"Articles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="Technology")
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = $this->articleService->getCategories();
        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/articles/meta/authors",
     *     summary="Get all available authors",
     *     description="Retrieve a list of all unique authors from stored articles",
     *     tags={"Articles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of authors",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="Jane Smith")
     *         )
     *     )
     * )
     */
    public function authors(): JsonResponse
    {
        $authors = $this->articleService->getAuthors();
        return $this->successResponse($authors, 'Authors retrieved successfully');
    }
}

