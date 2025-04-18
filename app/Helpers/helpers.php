<?php

if (!function_exists('format_currency')) {
    function format_currency($value)
    {
        return 'Rp. ' . number_format($value, 0, ',', '.');
    }
}
