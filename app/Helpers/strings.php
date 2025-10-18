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

if (!function_exists('getSubDomain')) {
    /**
     * Get the subdomain from the current request host.
     *
     * @return string|null
     */
    function getSubDomain(): ?string
    {
        $host = request()->getHost();
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            return $parts[0];
        }

        // For localhost or no subdomain case
        return null;
    }
}
