<?php

namespace App\Services\Auth;

use App\Entities\UserEntity;
use App\Services\ServiceInterface;

interface AuthServiceInterface extends ServiceInterface
{
    public function login(string $email, string $password): ?UserEntity;

    public function register(array $userData): ?UserEntity;

    public function logout(): bool;

    public function getCurrentUser(): ?UserEntity;

    public function isLoggedIn(): bool;

    public function isAdmin(): bool;
}