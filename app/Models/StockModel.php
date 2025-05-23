<?php

namespace App\Models;

use App\Entities\StockEntity;
use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table         = 'stock';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = StockEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['product_id', 'variation_id', 'quantity'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'product_id'   => 'required|integer|is_not_unique[products.id]',
        'variation_id' => 'permit_empty|integer',
        'quantity'     => 'required|integer|greater_than_equal_to[0]',
    ];

    public function getStockByProduct(int $productId, ?int $variationId = null)
    {
        $builder = $this->where('product_id', $productId);

        if ($variationId !== null) {
            $builder->where('variation_id', $variationId);
        } else {
            $builder->where('variation_id IS NULL');
        }

        return $builder->first();
    }

    public function updateStock(int $productId, ?int $variationId, int $quantity, bool $increment = false)
    {
        $stock = $this->getStockByProduct($productId, $variationId);

        if ($stock) {
            if ($increment) {
                $stock->quantity += $quantity;
            } else {
                $stock->quantity = $quantity;
            }

            return $this->save($stock);
        } else {
            $newStock = new StockEntity();
            $newStock->product_id = $productId;
            $newStock->variation_id = $variationId;
            $newStock->quantity = $quantity;

            return $this->insert($newStock);
        }
    }

    public function decrementStock(int $productId, ?int $variationId, int $quantity)
    {
        $stock = $this->getStockByProduct($productId, $variationId);

        if (!$stock || $stock->quantity < $quantity) {
            return false;
        }

        $stock->quantity -= $quantity;
        return $this->save($stock);
    }
}