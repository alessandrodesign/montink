<?php

namespace App\Entities;

use App\Enums\UserRole;
use App\ValueObjects\Email;
use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer',
    ];

    private Email $emailObject;
    private UserRole $roleEnum;

    public function setEmail(string $email): self
    {
        $this->emailObject = new Email($email);
        $this->attributes['email'] = (string)$this->emailObject;
        return $this;
    }

    public function getEmail(): Email
    {
        if (!isset($this->emailObject)) {
            $this->emailObject = new Email($this->attributes['email']);
        }
        return $this->emailObject;
    }

    public function setRole(UserRole|string $role): self
    {
        $this->roleEnum = $role instanceof UserRole ? $role : UserRole::fromString($role);
        $this->attributes['role'] = $this->roleEnum->value;
        return $this;
    }

    public function getRole(): UserRole
    {
        if (!isset($this->roleEnum)) {
            $this->roleEnum = UserRole::fromString($this->attributes['role']);
        }
        return $this->roleEnum;
    }

    public function setPassword(string $password): self
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }
}