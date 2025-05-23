<?php

namespace App\Services\Report;

use App\Services\BaseService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class ReportService extends BaseService implements ReportServiceInterface
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getSalesReport(string $startDate, string $endDate): array
    {
        $this->clearErrors();

        try {
            $builder = $this->db->table('orders');
            $builder->select('DATE(created_at) as date, COUNT(*) as order_count, SUM(final_amount) as total_sales');
            $builder->where('status !=', 'cancelled');
            $builder->where('created_at >=', $startDate . ' 00:00:00');
            $builder->where('created_at <=', $endDate . ' 23:59:59');
            $builder->groupBy('DATE(created_at)');
            $builder->orderBy('date', 'ASC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            $this->setError('database', $e->getMessage());
            return [];
        }
    }

    public function getProductSalesReport(string $startDate, string $endDate): array
    {
        $this->clearErrors();

        try {
            $builder = $this->db->table('order_items oi');
            $builder->select('p.id, p.name, SUM(oi.quantity) as quantity_sold, SUM(oi.price * oi.quantity) as total_sales');
            $builder->join('products p', 'oi.product_id = p.id');
            $builder->join('orders o', 'oi.order_id = o.id');
            $builder->where('o.status !=', 'cancelled');
            $builder->where('o.created_at >=', $startDate . ' 00:00:00');
            $builder->where('o.created_at <=', $endDate . ' 23:59:59');
            $builder->groupBy('p.id, p.name');
            $builder->orderBy('quantity_sold', 'DESC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            $this->setError('database', $e->getMessage());
            return [];
        }
    }

    public function getStockReport(): array
    {
        $this->clearErrors();

        try {
            $builder = $this->db->table('stock s');
            $builder->select('p.id, p.name, v.id as variation_id, v.name as variation_name, s.quantity');
            $builder->join('products p', 's.product_id = p.id');
            $builder->join('variations v', 's.variation_id = v.id', 'left');
            $builder->orderBy('p.name', 'ASC');
            $builder->orderBy('v.name', 'ASC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            $this->setError('database', $e->getMessage());
            return [];
        }
    }

    public function getLowStockProducts(int $threshold = 5): array
    {
        $this->clearErrors();

        try {
            $builder = $this->db->table('stock s');
            $builder->select('p.id, p.name, v.id as variation_id, v.name as variation_name, s.quantity');
            $builder->join('products p', 's.product_id = p.id');
            $builder->join('variations v', 's.variation_id = v.id', 'left');
            $builder->where('s.quantity <=', $threshold);
            $builder->orderBy('s.quantity', 'ASC');

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            $this->setError('database', $e->getMessage());
            return [];
        }
    }
}