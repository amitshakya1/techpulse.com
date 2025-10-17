<?php

use GeoIp2\Database\Reader;

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


if (!function_exists('getLocation')) {
    function getLocation()
    {
        $ip = request()->ip(); // or manually set an IP
        try {
            $reader = new Reader(storage_path('app/geoip/GeoLite2-City.mmdb'));
            $record = $reader->city($ip);

            return [
                'ip' => $ip,
                'city' => $record->city->name,
                'state' => $record->mostSpecificSubdivision->name,
                'country' => $record->country->name,
                'iso_code' => $record->country->isoCode,
                'latitude' => $record->location->latitude,
                'longitude' => $record->location->longitude,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}