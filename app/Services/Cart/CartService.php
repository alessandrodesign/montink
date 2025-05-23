<?php

namespace App\Services\Cart;

use App\Entities\CouponEntity;
use App\Enums\ProductStatus;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\VariationModel;
use App\Services\BaseService;
use App\Services\Coupon\CouponService;
use CodeIgniter\Session\Session;

class CartService extends BaseService implements CartServiceInterface
{
    protected Session $session;
    protected ProductModel $productModel;
    protected VariationModel $variationModel;
    protected StockModel $stockModel;
    protected CouponService $couponService;

    private static ?CartService $instance = null;

    public function __construct()
    {
        $this->session = session();
        $this->productModel = new ProductModel();
        $this->variationModel = new VariationModel();
        $this->stockModel = new StockModel();
        $this->couponService = new CouponService();

        // Initialize cart if not exists
        if (!$this->session->has('cart')) {
            $this->session->set('cart', [
                'items' => [],
                'coupon_id' => null,
            ]);
        }
    }

    public static function getInstance(): CartService
    {
        if (self::$instance === null) {
            self::$instance = new CartService();
        }
        return self::$instance;
    }

    public function count(): int
    {
        return $this->getItemCount();
    }

    public function getCart(): array
    {
        $cart = $this->session->get('cart');
        $items = [];

        foreach ($cart['items'] as $rowId => $item) {
            $product = $this->productModel->find($item['product_id']);

            if (!$product) {
                continue;
            }

            $variation = null;
            if ($item['variation_id']) {
                $variation = $this->variationModel->find($item['variation_id']);
            }

            $price = $product->getPrice()->getAmount();
            if ($variation) {
                $price += $variation->getPriceAdjustment()->getAmount();
            }

            $items[$rowId] = [
                'row_id' => $rowId,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variation_id' => $item['variation_id'],
                'variation_name' => $variation ? $variation->name : null,
                'price' => $price,
                'quantity' => $item['quantity'],
                'subtotal' => $price * $item['quantity'],
            ];
        }

        $coupon = null;
        if ($cart['coupon_id']) {
            $coupon = $this->couponService->getCoupon($cart['coupon_id']);
        }

        return [
            'items' => $items,
            'coupon' => $coupon,
            'subtotal' => $this->getSubtotal(),
            'discount' => $this->getDiscount(),
            'total' => $this->getTotal(),
            'shipping_fee' => $this->calculateShippingFee()
        ];
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variationId = null): bool
    {
        $this->clearErrors();

        $product = $this->productModel->find($productId);

        if (!$product) {
            $this->setError('product', 'Product not found');
            return false;
        }

        if ($product->status !== ProductStatus::ACTIVE) {
            $this->setError('product', 'Product is not available');
            return false;
        }

        if ($variationId) {
            $variation = $this->variationModel->find($variationId);
            if (!$variation || $variation->product_id != $productId) {
                $this->setError('variation', 'Invalid variation');
                return false;
            }
        }

        // Check stock
        $stock = $this->stockModel->getStockByProduct($productId, $variationId);
        if (!$stock || $stock->quantity < $quantity) {
            $this->setError('stock', 'Insufficient stock');
            return false;
        }

        $cart = $this->session->get('cart');
        $rowId = $this->generateRowId($productId, $variationId);

        // If item already exists, update quantity
        if (isset($cart['items'][$rowId])) {
            $newQuantity = $cart['items'][$rowId]['quantity'] + $quantity;

            if ($stock->quantity < $newQuantity) {
                $this->setError('stock', 'Insufficient stock');
                return false;
            }

            $cart['items'][$rowId]['quantity'] = $newQuantity;
        } else {
            $cart['items'][$rowId] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity,
            ];
        }

        $this->session->set('cart', $cart);
        return true;
    }

    public function updateItem(string $rowId, int $quantity): bool
    {
        $this->clearErrors();

        $cart = $this->session->get('cart');

        if (!isset($cart['items'][$rowId])) {
            $this->setError('item', 'Item not found in cart');
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeItem($rowId);
        }

        $item = $cart['items'][$rowId];

        // Check stock
        $stock = $this->stockModel->getStockByProduct($item['product_id'], $item['variation_id']);
        if (!$stock || $stock->quantity < $quantity) {
            $this->setError('stock', 'Insufficient stock');
            return false;
        }

        $cart['items'][$rowId]['quantity'] = $quantity;
        $this->session->set('cart', $cart);

        return true;
    }

    public function removeItem(string $rowId): bool
    {
        $cart = $this->session->get('cart');

        if (isset($cart['items'][$rowId])) {
            unset($cart['items'][$rowId]);
            $this->session->set('cart', $cart);
        }

        return true;
    }

    public function clear(): bool
    {
        $this->session->set('cart', [
            'items' => [],
            'coupon_id' => null,
        ]);

        return true;
    }

    public function getTotal(): float
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $shippingFee = $this->calculateShippingFee();

        return max($subtotal - $discount + $shippingFee, 0);
    }

    public function getSubtotal(): float
    {
        $cart = $this->session->get('cart');
        $subtotal = 0;

        foreach ($cart['items'] as $item) {
            $product = $this->productModel->find($item['product_id']);

            if (!$product) {
                continue;
            }

            $price = $product->getPrice()->getAmount();

            if ($item['variation_id']) {
                $variation = $this->variationModel->find($item['variation_id']);
                if ($variation) {
                    $price += $variation->getPriceAdjustment()->getAmount();
                }
            }

            $subtotal += $price * $item['quantity'];
        }

        return $subtotal;
    }

    public function getDiscount(): float
    {
        $cart = $this->session->get('cart');

        if (!$cart['coupon_id']) {
            return 0;
        }

        $coupon = $this->couponService->getCoupon($cart['coupon_id']);

        if (!$coupon || !$coupon->isValid()) {
            $this->removeCoupon();
            return 0;
        }

        return $this->couponService->calculateDiscount($coupon, $this->getSubtotal());
    }

    public function applyCoupon(CouponEntity $coupon): bool
    {
        $cart = $this->session->get('cart');
        $cart['coupon_id'] = $coupon->id;
        $this->session->set('cart', $cart);

        return true;
    }

    public function removeCoupon(): bool
    {
        $cart = $this->session->get('cart');
        $cart['coupon_id'] = null;
        $this->session->set('cart', $cart);

        return true;
    }

    public function getCoupon(): ?CouponEntity
    {
        $cart = $this->session->get('cart');

        if (!$cart['coupon_id']) {
            return null;
        }

        return $this->couponService->getCoupon($cart['coupon_id']);
    }

    public function getItemCount(): int
    {
        $cart = $this->session->get('cart');
        $count = 0;

        foreach ($cart['items'] as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    public function hasItems(): bool
    {
        $cart = $this->session->get('cart');
        return !empty($cart['items']);
    }

    public function validateStock(): bool
    {
        $this->clearErrors();
        $cart = $this->session->get('cart');
        $valid = true;

        foreach ($cart['items'] as $rowId => $item) {
            $stock = $this->stockModel->getStockByProduct($item['product_id'], $item['variation_id']);

            if (!$stock || $stock->quantity < $item['quantity']) {
                $product = $this->productModel->find($item['product_id']);
                $productName = $product ? $product->name : 'Product';

                if ($item['variation_id']) {
                    $variation = $this->variationModel->find($item['variation_id']);
                    if ($variation) {
                        $productName .= ' - ' . $variation->name;
                    }
                }

                $this->setError($rowId, "Insufficient stock for {$productName}");
                $valid = false;
            }
        }

        return $valid;
    }

    protected function generateRowId(int $productId, ?int $variationId): string
    {
        return md5($productId . '-' . ($variationId ?? 'null'));
    }

    public function calculateShippingFee(): float
    {
        $subtotal = $this->getSubtotal();

        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } elseif ($subtotal > 200.00) {
            return 0.00;
        } else {
            return 20.00;
        }
    }
}