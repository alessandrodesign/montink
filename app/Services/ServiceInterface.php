<?php

namespace App\Services;

interface ServiceInterface
{
    public function getErrors(): array;

    public function hasErrors(): bool;
}