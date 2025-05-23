<?php

use App\Libraries\Auth;

/**
 * Função helper para acessar a instância singleton Auth
 *
 * Exemplo de uso:
 *   auth()->isLogged();
 *   auth()->userName();
 */
if (!function_exists('auth')) {
    function auth(): Auth
    {
        return Auth::getInstance();
    }
}