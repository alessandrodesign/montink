<?php

if (!function_exists("tf")) {
    function tf($string, ...$values): string
    {
        return sprintf($string, ...$values);
    }
}

if (!function_exists("t")) {
    function t($string): string
    {
        if (!$string) {
            return '';
        }

        return esc($string);
    }
}