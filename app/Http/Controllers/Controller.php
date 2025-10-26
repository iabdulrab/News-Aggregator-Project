<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="A comprehensive news aggregator API that fetches articles from NewsAPI, The Guardian, and The New York Times. Provides advanced search, filtering, and personalization features.",
 *     @OA\Contact(
 *         email="support@newsaggregator.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token in the format: Bearer {token}"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and authorization endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Articles",
 *     description="Article search, filtering, and retrieval endpoints with auto-fetch feature"
 * )
 *
 * @OA\Tag(
 *     name="Sources",
 *     description="News source management endpoints"
 * )
 *
 * @OA\Tag(
 *     name="User Preferences",
 *     description="User preference management for personalized feeds"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
