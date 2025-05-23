<?php

namespace App\Models;

use App\Entities\OrderItemEntity;
use CodeIgniter\Model;
use Config\Database;

class OrderItemModel extends Model
{
    protected $table         = 'order_items';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = OrderItemEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['order_id', 'product_id', 'variation_id', 'quantity', 'price'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'order_id'     => 'required|integer|is_not_unique[orders.id]',
        'product_id'   => 'required|integer|is_not_unique[products.id]',
        'variation_id' => 'permit_empty',
        'quantity'     => 'required|integer|greater_than[0]',
        'price'        => 'required|numeric|greater_than[0]',
    ];

    public function findByOrder(int $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    public function getItemsWithProductDetails(int $orderId): array
    {
        $db = Database::connect();
        $builder = $db->table('order_items oi');
        $builder->select('oi.*, p.name as product_name, v.name as variation_name');
        $builder->join('products p', 'oi.product_id = p.id');
        $builder->join('variations v', 'oi.variation_id = v.id', 'left');
        $builder->where('oi.order_id', $orderId);
        $query = $builder->get();

        $result = [];
        foreach ($query->getResult() as $row) {
            $item = new OrderItemEntity();
            $item->fill((array) $row);
            $item->product_name = $row->product_name;
            $item->variation_name = $row->variation_name;
            $result[] = $item;
        }

        return $result;
    }
}