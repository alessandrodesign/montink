<?php

namespace App\Libraries;

use App\Services\Auth\AuthService;
use App\Entities\UserEntity;

class Auth
{
    private static ?Auth $instance = null;
    private AuthService $authService;
    private ?UserEntity $currentUser = null;

    private function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Retorna a instância singleton da classe Auth
     */
    public static function getInstance(): Auth
    {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    /**
     * Verifica se o usuário está logado
     */
    public function isLogged(): bool
    {
        return $this->authService->isLoggedIn();
    }

    /**
     * Retorna o usuário atual (UserEntity) ou null
     */
    public function user(): ?UserEntity
    {
        if ($this->currentUser === null) {
            $this->currentUser = $this->authService->getCurrentUser();
        }
        return $this->currentUser;
    }

    /**
     * Retorna o ID do usuário logado ou null
     */
    public function userId(): ?int
    {
        $user = $this->user();
        return $user?->id;
    }

    /**
     * Retorna o nome do usuário logado ou null
     */
    public function userName(): ?string
    {
        $user = $this->user();
        return $user?->name;
    }


    /**
     * Retorna apenas o primeiro nome do usuário logado ou null
     */
    public function userFirstName(): ?string
    {
        $user = $this->user();
        return isset($user->name) ? explode(" ", $user->name)[0] : null;
    }

    /**
     * Retorna o email do usuário logado ou null
     */
    public function userEmail(): ?string
    {
        $user = $this->user();
        return $user?->email;
    }

    /**
     * Retorna o papel do usuário logado ou null
     */
    public function userRole(): ?string
    {
        $user = $this->user();
        return $user?->role;
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return $this->authService->isAdmin();
    }

    /**
     * Realiza logout do usuário
     */
    public function logout(): bool
    {
        $this->currentUser = null;
        return $this->authService->logout();
    }
}