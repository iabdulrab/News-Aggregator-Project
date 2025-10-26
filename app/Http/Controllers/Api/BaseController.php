<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response with data
     * 
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Success response without data
     * 
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successMessage(string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Paginated success response
     * 
     * @param mixed $paginatedData
     * @param string $message
     * @return JsonResponse
     */
    protected function paginatedResponse($paginatedData, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginatedData->items(),
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'last_page' => $paginatedData->lastPage(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
                'links' => [
                    'first' => $paginatedData->url(1),
                    'last' => $paginatedData->url($paginatedData->lastPage()),
                    'prev' => $paginatedData->previousPageUrl(),
                    'next' => $paginatedData->nextPageUrl(),
                ]
            ]
        ], 200);
    }

    /**
     * Error response
     * 
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Not found response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Validation error response
     * 
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Unauthorized response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Forbidden response
     * 
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Created response (for POST requests)
     * 
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Success response with custom headers
     * 
     * @param mixed $data
     * @param string $message
     * @param array $headers
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponseWithHeaders($data, string $message, array $headers, int $statusCode = 200): JsonResponse
    {
        $response = response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }

    /**
     * Paginated response with custom headers
     * 
     * @param mixed $paginatedData
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    protected function paginatedResponseWithHeaders($paginatedData, string $message, array $headers): JsonResponse
    {
        $response = $this->paginatedResponse($paginatedData, $message);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}

