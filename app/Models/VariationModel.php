<?php

namespace App\Models;

use App\Entities\VariationEntity;
use CodeIgniter\Model;

class VariationModel extends Model
{
    protected $table         = 'variations';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = VariationEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['product_id', 'name', 'price_adjustment'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'product_id'       => 'required|integer|is_not_unique[products.id]',
        'name'             => 'required|min_length[1]|max_length[255]',
        'price_adjustment' => 'required|numeric',
    ];

    public function findByProduct(int $productId)
    {
        return $this->where('product_id', $productId)->findAll();
    }
}