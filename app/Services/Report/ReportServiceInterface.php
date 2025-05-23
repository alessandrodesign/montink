<?php

namespace App\Services\Report;

use App\Services\ServiceInterface;

interface ReportServiceInterface extends ServiceInterface
{
    public function getSalesReport(string $startDate, string $endDate): array;

    public function getProductSalesReport(string $startDate, string $endDate): array;

    public function getStockReport(): array;

    public function getLowStockProducts(int $threshold = 5): array;
}