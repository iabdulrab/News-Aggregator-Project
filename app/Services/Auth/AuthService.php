<?php

namespace App\Services\Auth;

use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function loginUser($data)
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
