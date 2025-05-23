<?php

namespace App\Services\Product;

use App\Entities\ProductEntity;
use App\Entities\VariationEntity;
use App\Services\ServiceInterface;

interface ProductServiceInterface extends ServiceInterface
{
    public function getAllProducts(bool $activeOnly = true): array;

    public function getProduct(int $id): ?ProductEntity;

    public function createProduct(array $data): ?ProductEntity;

    public function updateProduct(int $id, array $data): ?ProductEntity;

    public function deleteProduct(int $id): bool;

    public function getProductWithVariations(int $id): ?ProductEntity;

    public function addVariation(int $productId, array $data): ?VariationEntity;

    public function updateVariation(int $variationId, array $data): ?VariationEntity;

    public function deleteVariation(int $variationId): bool;

    public function updateStock(int $productId, ?int $variationId, int $quantity): bool;
}