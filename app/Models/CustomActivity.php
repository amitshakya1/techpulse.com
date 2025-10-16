<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as BaseActivity;

class CustomActivity extends BaseActivity
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid',
        'store_id',
        'ip_address',
    ];

    protected static function booted()
    {
        $store_id = BaseModel::resolveStoreId();
        static::creating(function ($activity) use ($store_id) {
            $activity->store_id = $store_id;
            $activity->ip_address = request()->ip();
        });
    }
}

