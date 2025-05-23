<?php

use App\Services\Cart\CartService;

if (!function_exists('image_url')) {
    function image_url($img): string
    {
        $img = 'image/' . str_replace('\\', '/', $img);
        return base_url($img);
    }
}

if (!function_exists('cart')) {
    /**
     * Helper para acessar a instância do CartService
     *
     * @return CartService
     */
    function cart(): CartService
    {
        return CartService::getInstance();
    }
}