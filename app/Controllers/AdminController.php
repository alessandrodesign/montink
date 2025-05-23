<?php

namespace App\Controllers;

use App\Services\Report\ReportService;

class AdminController extends BaseController
{
    protected ReportService $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    public function index()
    {
        $this->requireAdmin();

        // Get some basic stats for the dashboard
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate);
        $productSalesReport = $this->reportService->getProductSalesReport($startDate, $endDate);
        $lowStockProducts = $this->reportService->getLowStockProducts();

        $data = [
            'title' => 'Admin Dashboard',
            'salesReport' => $salesReport,
            'productSalesReport' => $productSalesReport,
            'lowStockProducts' => $lowStockProducts,
        ];

        return view('admin/dashboard', $data);
    }

    public function salesReport()
    {
        $this->requireAdmin();

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate);

        $data = [
            'title' => 'Sales Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesReport' => $salesReport,
        ];

        return view('admin/reports/sales', $data);
    }

    public function productSalesReport()
    {
        $this->requireAdmin();

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $productSalesReport = $this->reportService->getProductSalesReport($startDate, $endDate);

        $data = [
            'title' => 'Product Sales Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productSalesReport' => $productSalesReport,
        ];

        return view('admin/reports/product_sales', $data);
    }

    public function stockReport()
    {
        $this->requireAdmin();

        $stockReport = $this->reportService->getStockReport();

        $data = [
            'title' => 'Stock Report',
            'stockReport' => $stockReport,
        ];

        return view('admin/reports/stock', $data);
    }

    public function lowStockReport()
    {
        $this->requireAdmin();

        $threshold = $this->request->getGet('threshold') ?? 5;
        $lowStockProducts = $this->reportService->getLowStockProducts($threshold);

        $data = [
            'title' => 'Low Stock Report',
            'threshold' => $threshold,
            'lowStockProducts' => $lowStockProducts,
        ];

        return view('admin/reports/low_stock', $data);
    }
}