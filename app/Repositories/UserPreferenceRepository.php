<?php

namespace App\Repositories;

use App\Interfaces\UserPreferenceRepositoryInterface;
use App\Models\UserPreference;

class UserPreferenceRepository implements UserPreferenceRepositoryInterface
{
    public function getUserPreference(int $userId)
    {
        return UserPreference::where('user_id', $userId)->first();
    }

    public function updateOrCreate(int $userId, array $preferences)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            ['preferences' => $preferences]
        );
    }

    public function delete(int $userId): bool
    {
        return UserPreference::where('user_id', $userId)->delete();
    }
}

