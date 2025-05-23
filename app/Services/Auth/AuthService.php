<?php

namespace App\Services\Auth;

use App\Entities\UserEntity;
use App\Enums\UserRole;
use App\Models\UserModel;
use App\Services\BaseService;
use CodeIgniter\Session\Session;

class AuthService extends BaseService implements AuthServiceInterface
{
    public UserModel $userModel;
    protected Session $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    public function login(string $email, string $password): ?UserEntity
    {
        $this->clearErrors();

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->setError('login', 'Invalid email or password');
            return null;
        }

        if (!$user->verifyPassword($password)) {
            $this->setError('login', 'Invalid email or password');
            return null;
        }

        $this->session->set('user_id', $user->id);
        $this->session->set('user_email', $user->email);
        $this->session->set('user_role', $user->role);

        return $user;
    }

    public function register(array $userData): ?UserEntity
    {
        $this->clearErrors();

        $user = new UserEntity($userData);

        if (!$this->userModel->save($user)) {
            $this->errors = $this->userModel->errors();
            return null;
        }

        $user->id = $this->userModel->getInsertID();

        return $user;
    }

    public function updateProfile(array $userData): ?UserEntity
    {
        $this->clearErrors();

        $user = new UserEntity($userData);

        if (!$this->userModel->save($user)) {
            $this->errors = $this->userModel->errors();
            return null;
        }

        return $user;
    }

    public function logout(): bool
    {
        $this->session->remove(['user_id', 'user_email', 'user_role']);
        return true;
    }

    public function getCurrentUser(): ?UserEntity
    {
        $userId = $this->session->get('user_id');

        if (!$userId) {
            return null;
        }

        return $this->userModel->find($userId);
    }

    public function isLoggedIn(): bool
    {
        return $this->session->has('user_id');
    }

    public function isAdmin(): bool
    {
        return $this->session->get('user_role') === UserRole::ADMIN;
    }
}