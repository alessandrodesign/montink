<?php

namespace App\Controllers;

use App\Models\VariationModel;
use App\Services\Product\ProductService;

class ProductController extends BaseController
{
    protected ProductService $productService;
    protected VariationModel $variationModel;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->variationModel = new VariationModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Products',
            'products' => $this->productService->getAllProducts(false),
        ];

        return view('product/index', $data);
    }

    public function view($id)
    {
        $product = $this->productService->getProductWithVariations($id);

        if (!$product) {
            $this->setMessage('Product not found', 'error');
            return redirect()->to('/products');
        }

        $data = [
            'title' => $product->name,
            'product' => $product,
        ];

        return view('product/view', $data);
    }

    public function create()
    {
        $this->requireAdmin();

        if ($this->request->is('post')) {
            $productData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'price' => $this->request->getPost('price'),
                'status' => $this->request->getPost('status'),
                'initial_stock' => $this->request->getPost('initial_stock'),
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $productData['image'] = $image;
            }

            $product = $this->productService->createProduct($productData);

            if ($product) {
                $this->setMessage('Product created successfully');
                return redirect()->to('/products/view/' . $product->id);
            } else {
                $this->setValidationErrors($this->productService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Create Product',
        ];

        return view('product/create', $data);
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $product = $this->productService->getProductWithVariations($id);

        if (!$product) {
            $this->setMessage('Product not found', 'error');
            return redirect()->to('/products');
        }

        if ($this->request->is('post')) {
            $productData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'price' => $this->request->getPost('price'),
                'status' => $this->request->getPost('status'),
            ];

            // Handle image upload
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $productData['image'] = $image;
            }

            $product = $this->productService->updateProduct($id, $productData);

            if ($product) {
                $this->setMessage('Product updated successfully');
                return redirect()->to('/products/view/' . $product->id);
            } else {
                $this->setValidationErrors($this->productService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Edit Product',
            'product' => $product,
        ];

        return view('product/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAdmin();

        if ($this->productService->deleteProduct($id)) {
            $this->setMessage('Product deleted successfully');
        } else {
            $this->setMessage('Failed to delete product', 'error');
        }

        return redirect()->to('/products');
    }

    public function addVariation($productId)
    {
        $this->requireAdmin();

        $product = $this->productService->getProduct($productId);

        if (!$product) {
            $this->setMessage('Product not found', 'error');
            return redirect()->to('/products');
        }

        if ($this->request->is('post')) {
            $variationData = [
                'name' => $this->request->getPost('name'),
                'price_adjustment' => $this->request->getPost('price_adjustment'),
                'initial_stock' => $this->request->getPost('initial_stock'),
            ];

            $variation = $this->productService->addVariation($productId, $variationData);

            if ($variation) {
                $this->setMessage('Variation added successfully');
                return redirect()->to('/products/view/' . $productId);
            } else {
                $this->setValidationErrors($this->productService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Add Variation',
            'product' => $product,
        ];

        return view('product/add_variation', $data);
    }

    public function editVariation($variationId)
    {
        $this->requireAdmin();

        // Get variation and product
        $variation = $this->variationModel->find($variationId);

        if (!$variation) {
            $this->setMessage('Variation not found', 'error');
            return redirect()->to('/products');
        }

        $product = $this->productService->getProduct($variation->product_id);

        if ($this->request->is('post')) {
            $variationData = [
                'name' => $this->request->getPost('name'),
                'price_adjustment' => $this->request->getPost('price_adjustment'),
            ];

            $variation = $this->productService->updateVariation($variationId, $variationData);

            if ($variation) {
                $this->setMessage('Variation updated successfully');
                return redirect()->to('/products/view/' . $variation->product_id);
            } else {
                $this->setValidationErrors($this->productService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Edit Variation',
            'product' => $product,
            'variation' => $variation,
        ];

        return view('product/edit_variation', $data);
    }

    public function deleteVariation($variationId)
    {
        $this->requireAdmin();

        // Get variation to get product_id for redirect
        $variation = $this->variationModel->find($variationId);

        if (!$variation) {
            $this->setMessage('Variation not found', 'error');
            return redirect()->to('/products');
        }

        $productId = $variation->product_id;

        if ($this->productService->deleteVariation($variationId)) {
            $this->setMessage('Variation deleted successfully');
        } else {
            $this->setMessage('Failed to delete variation', 'error');
        }

        return redirect()->to('/products/view/' . $productId);
    }

    public function updateStock($productId, $variationId = null)
    {
        $this->requireAdmin();

        $product = $this->productService->getProduct($productId);

        if (!$product) {
            $this->setMessage('Product not found', 'error');
            return redirect()->to('/products');
        }

        if ($variationId) {
            $variation = $this->variationModel->find($variationId);
            if (!$variation || $variation->product_id != $productId) {
                $this->setMessage('Variation not found', 'error');
                return redirect()->to('/products/view/' . $productId);
            }
        }

        if ($this->request->is('post')) {
            $quantity = (int) $this->request->getPost('quantity');

            if ($this->productService->updateStock($productId, $variationId, $quantity)) {
                $this->setMessage('Stock updated successfully');
                return redirect()->to('/products/view/' . $productId);
            } else {
                $this->setValidationErrors($this->productService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Update Stock',
            'product' => $product,
            'variation_id' => $variationId,
        ];

        if ($variationId) {
            $data['variation'] = $this->variationModel->find($variationId);
        }

        return view('product/update_stock', $data);
    }
}