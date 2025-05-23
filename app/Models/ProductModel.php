<?php

namespace App\Models;

use App\Entities\ProductEntity;
use CodeIgniter\Model;
use Config\Database;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = ProductEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'description', 'price', 'image', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'        => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty',
        'price'       => 'required|numeric|greater_than[0]',
        'image'       => 'permit_empty',
        'status'      => 'required|in_list[active,inactive]',
    ];

    public function findActive()
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getWithStock(): array
    {
        $db = Database::connect();
        $builder = $db->table('products p');
        $builder->select('p.*, COALESCE(SUM(s.quantity), 0) as stock_quantity');
        $builder->join('stock s', 'p.id = s.product_id', 'left');
        $builder->where('p.status', 'active');
        $builder->groupBy('p.id');
        $query = $builder->get();

        $result = [];
        foreach ($query->getResult() as $row) {
            $product = new ProductEntity();
            $product->fill((array) $row);
            $product->stock_quantity = (int) $row->stock_quantity;
            $result[] = $product;
        }

        return $result;
    }
}