<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $fillable = [
        'model_type',
        'model_id',
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'store_id',        // ✅ your custom column
        'ip_address',      // ✅ optional (if you add it)
    ];

    protected static function booted()
    {
        $store_id = BaseModel::resolveStoreId();
        static::creating(function ($media) use ($store_id) {
            $media->store_id = $store_id;
            $media->ip_address = request()->ip();
        });
    }
}
