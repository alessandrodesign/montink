<?php

namespace App\Services\Product;

use App\Entities\ProductEntity;
use App\Entities\VariationEntity;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariationModel;
use App\Services\BaseService;
use CodeIgniter\Files\File;

class ProductService extends BaseService implements ProductServiceInterface
{
    protected ProductModel $productModel;
    protected VariationModel $variationModel;
    protected StockModel $stockModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->variationModel = new VariationModel();
        $this->stockModel = new StockModel();
    }

    public function getAllProducts(bool $activeOnly = true): array
    {
        if ($activeOnly) {
            return $this->productModel->findActive();
        }

        return $this->productModel->findAll();
    }

    public function getProduct(int $id): ?ProductEntity
    {
        return $this->productModel->find($id);
    }

    public function createProduct(array $data): ?ProductEntity
    {
        $this->clearErrors();

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof File) {
            $newName = $this->handleImageUpload($data['image']);
            if ($newName) {
                $data['image'] = $newName;
            } else {
                $this->setError('image', 'Failed to upload image');
                return null;
            }
        }

        $product = new ProductEntity($data);

        if (!$this->productModel->save($product)) {
            $this->errors = $this->productModel->errors();
            return null;
        }

        $product->id = $this->productModel->getInsertID();

        // Create initial stock entry
        if (isset($data['initial_stock']) && is_numeric($data['initial_stock'])) {
            $this->stockModel->updateStock($product->id, null, (int)$data['initial_stock']);
        }

        return $product;
    }

    public function updateProduct(int $id, array $data): ?ProductEntity
    {
        $this->clearErrors();

        $product = $this->productModel->find($id);

        if (!$product) {
            $this->setError('id', 'Product not found');
            return null;
        }

        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof File) {
            $newName = $this->handleImageUpload($data['image']);
            if ($newName) {
                // Delete old image if exists
                if ($product->image && file_exists(FCPATH . 'uploads/products/' . $product->image)) {
                    unlink(FCPATH . 'uploads/products/' . $product->image);
                }
                $data['image'] = $newName;
            } else {
                $this->setError('image', 'Failed to upload image');
                return null;
            }
        }

        $product->fill($data);

        if (!$this->productModel->save($product)) {
            $this->errors = $this->productModel->errors();
            return null;
        }

        return $product;
    }

    public function deleteProduct(int $id): bool
    {
        $this->clearErrors();

        $product = $this->productModel->find($id);

        if (!$product) {
            $this->setError('id', 'Product not found');
            return false;
        }

        // Delete product image if exists
        if ($product->image && file_exists(FCPATH . 'uploads/products/' . $product->image)) {
            unlink(FCPATH . 'uploads/products/' . $product->image);
        }

        return $this->productModel->delete($id);
    }

    public function getProductWithVariations(int $id): ?ProductEntity
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return null;
        }

        $product->variations = $this->variationModel->findByProduct($id);

        // Get stock for base product
        $baseStock = $this->stockModel->getStockByProduct($id);
        $product->stock = $baseStock ? $baseStock->quantity : 0;

        // Get stock for each variation
        foreach ($product->variations as $variation) {
            $variationStock = $this->stockModel->getStockByProduct($id, $variation->id);
            $variation->stock = $variationStock ? $variationStock->quantity : 0;
        }

        return $product;
    }

    public function addVariation(int $productId, array $data): ?VariationEntity
    {
        $this->clearErrors();

        if (!$this->productModel->find($productId)) {
            $this->setError('product_id', 'Product not found');
            return null;
        }

        $data['product_id'] = $productId;
        $variation = new VariationEntity($data);

        if (!$this->variationModel->save($variation)) {
            $this->errors = $this->variationModel->errors();
            return null;
        }

        $variation->id = $this->variationModel->getInsertID();

        // Create initial stock entry for variation
        if (isset($data['initial_stock']) && is_numeric($data['initial_stock'])) {
            $this->stockModel->updateStock($productId, $variation->id, (int)$data['initial_stock']);
        }

        return $variation;
    }

    public function updateVariation(int $variationId, array $data): ?VariationEntity
    {
        $this->clearErrors();

        $variation = $this->variationModel->find($variationId);

        if (!$variation) {
            $this->setError('id', 'Variation not found');
            return null;
        }

        $variation->fill($data);

        if (!$this->variationModel->save($variation)) {
            $this->errors = $this->variationModel->errors();
            return null;
        }

        return $variation;
    }

    public function deleteVariation(int $variationId): bool
    {
        $this->clearErrors();

        $variation = $this->variationModel->find($variationId);

        if (!$variation) {
            $this->setError('id', 'Variation not found');
            return false;
        }

        return $this->variationModel->delete($variationId);
    }

    public function updateStock(int $productId, ?int $variationId, int $quantity): bool
    {
        $this->clearErrors();

        if (!$this->productModel->find($productId)) {
            $this->setError('product_id', 'Product not found');
            return false;
        }

        if ($variationId && !$this->variationModel->find($variationId)) {
            $this->setError('variation_id', 'Variation not found');
            return false;
        }

        return $this->stockModel->updateStock($productId, $variationId, $quantity);
    }

    protected function handleImageUpload(File $file): ?string
    {
        if (!$file->isValid() || $file->getError() !== 0) {
            return null;
        }

        $type = $file->getMimeType();
        if (!in_array($type, ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'])) {
            return null;
        }

        $newName = $file->getRandomName();
        $path = 'uploads/products';
        $uploadPath = WRITEPATH . $path;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($file->move($uploadPath, $newName)) {
            return $path . DIRECTORY_SEPARATOR . $newName;
        }

        return null;
    }
}