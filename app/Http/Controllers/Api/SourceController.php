<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SourceResource;
use App\Services\Source\SourceService;
use Illuminate\Http\JsonResponse;

class SourceController extends BaseController
{
    protected SourceService $sourceService;

    public function __construct(SourceService $sourceService)
    {
        $this->sourceService = $sourceService;
    }
    /**
     * @OA\Get(
     *     path="/api/sources",
     *     summary="Get all news sources",
     *     description="Retrieve a list of all available news sources with article counts",
     *     tags={"Sources"},
     *     @OA\Response(
     *         response=200,
     *         description="List of sources",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="key", type="string", example="newsapi"),
     *                 @OA\Property(property="name", type="string", example="NewsAPI"),
     *                 @OA\Property(property="base_url", type="string", example="https://newsapi.org/v2"),
     *                 @OA\Property(property="meta", type="object"),
     *                 @OA\Property(property="articles_count", type="integer", example=1250)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $sources = $this->sourceService->getAllSources();
        return $this->successResponse(SourceResource::collection($sources), 'Sources retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/sources/{id}",
     *     summary="Get a single news source",
     *     description="Retrieve detailed information about a specific news source",
     *     tags={"Sources"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Source ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Source information",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="key", type="string", example="newsapi"),
     *             @OA\Property(property="name", type="string", example="NewsAPI"),
     *             @OA\Property(property="base_url", type="string", example="https://newsapi.org/v2"),
     *             @OA\Property(property="articles_count", type="integer", example=1250)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Source not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Source not found")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $source = $this->sourceService->getSourceById($id);

        if (!$source) {
            return $this->notFoundResponse('Source not found');
        }

        return $this->successResponse($source, 'Source retrieved successfully');
    }
}

