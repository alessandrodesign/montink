<?php

namespace App\Services;

abstract class BaseService implements ServiceInterface
{
    protected array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function setError(string $key, string $message): void
    {
        $this->errors[$key] = $message;
    }

    protected function clearErrors(): void
    {
        $this->errors = [];
    }
}