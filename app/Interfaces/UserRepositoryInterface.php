<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findByEmail($email): ?User;
}
