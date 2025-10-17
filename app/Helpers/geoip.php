<?php

use GeoIp2\Database\Reader;

if (!function_exists('getIpLocation')) {
    function getIpLocation()
    {
        $ip = request()->ip();
        try {
            $ip = $ip == "127.0.0.1" ? "182.69.180.69" : $ip;
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