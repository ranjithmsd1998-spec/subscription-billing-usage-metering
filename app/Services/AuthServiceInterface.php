<?php

namespace App\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(array $data): array;

    public function logout(User $user): bool;
}