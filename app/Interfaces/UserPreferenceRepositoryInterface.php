<?php

namespace App\Interfaces;

interface UserPreferenceRepositoryInterface
{
    public function getUserPreference(int $userId);
    
    public function updateOrCreate(int $userId, array $preferences);
    
    public function delete(int $userId): bool;
}

