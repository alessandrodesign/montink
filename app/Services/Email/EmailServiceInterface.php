<?php

namespace App\Services\Email;

use App\Entities\OrderEntity;
use App\Entities\UserEntity;
use App\Services\ServiceInterface;

interface EmailServiceInterface extends ServiceInterface
{
    public function sendOrderConfirmation(OrderEntity $order): bool;

    public function sendWelcomeEmail(UserEntity $user): bool;

    public function sendPasswordReset(UserEntity $user, string $token): bool;
}