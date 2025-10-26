<?php

namespace App\Services\UserPreference;

use App\Interfaces\UserPreferenceRepositoryInterface;

class UserPreferenceService
{
    protected UserPreferenceRepositoryInterface $preferenceRepository;

    public function __construct(UserPreferenceRepositoryInterface $preferenceRepository)
    {
        $this->preferenceRepository = $preferenceRepository;
    }

    public function getUserPreference(int $userId)
    {
        $preference = $this->preferenceRepository->getUserPreference($userId);

        if (!$preference) {
            return [
                'preferences' => [
                    'sources' => [],
                    'categories' => [],
                    'authors' => []
                ]
            ];
        }

        return $preference;
    }

    public function updateUserPreference(int $userId, array $preferences)
    {
        return $this->preferenceRepository->updateOrCreate($userId, $preferences);
    }

    public function deleteUserPreference(int $userId): bool
    {
        return $this->preferenceRepository->delete($userId);
    }
}

