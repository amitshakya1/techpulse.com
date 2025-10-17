<?php

if (!function_exists('greet')) {
    function greet($name)
    {
        return "Hello, " . $name . "!";
    }
}

if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format($price, 2);
    }
}

if (!function_exists('messages')) {
    function messages(string $key, string $attribute): string
    {
        $message = config("messages.$key", ':attribute');
        return str_replace(':attribute', $attribute, $message);
    }
}
