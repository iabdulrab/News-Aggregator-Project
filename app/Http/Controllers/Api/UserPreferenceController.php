<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreference\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends BaseController
{
    protected UserPreferenceService $preferenceService;

    public function __construct(UserPreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }
    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     description="Retrieve the authenticated user's saved preferences for sources, categories, and authors",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="preferences", type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="string", example="newsapi")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string", example="Technology")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="string", example="Jane Smith"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $preference = $this->preferenceService->getUserPreference($user->id);

        return $this->successResponse(new UserPreferenceResource($preference), 'Preferences retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/preferences",
     *     summary="Update user preferences",
     *     description="Update the authenticated user's preferences for personalized article feed",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"preferences"},
     *             @OA\Property(property="preferences", type="object",
     *                 @OA\Property(property="sources", type="array",
     *                     @OA\Items(type="string", enum={"newsapi", "guardian", "nytimes"}),
     *                     example={"newsapi", "guardian"}
     *                 ),
     *                 @OA\Property(property="categories", type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Technology", "Business"}
     *                 ),
     *                 @OA\Property(property="authors", type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Jane Smith"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully"),
     *             @OA\Property(property="preference", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'preferences' => 'required|array',
            'preferences.sources' => 'array',
            'preferences.categories' => 'array',
            'preferences.authors' => 'array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Validation failed'
            );
        }

        $user = $request->user();
        
        $preference = $this->preferenceService->updateUserPreference(
            $user->id, 
            $request->input('preferences')
        );

        return $this->successResponse($preference, 'Preferences updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/preferences",
     *     summary="Reset user preferences",
     *     description="Delete the authenticated user's preferences and reset to defaults",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Preferences reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences reset successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->preferenceService->deleteUserPreference($user->id);

        return $this->successMessage('Preferences reset successfully');
    }
}

