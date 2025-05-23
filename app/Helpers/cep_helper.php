<?php

if (!function_exists('getAddressByCep')) {
    function getAddressByCep(string $cep): ?array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $response = @file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (isset($data['erro'])) {
            return null;
        }

        return $data;
    }
}