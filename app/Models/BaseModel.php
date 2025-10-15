<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Automatically set store_id when creating a new record.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->store_id)) {
                $storeId = static::resolveStoreId();
                if ($storeId) {
                    $model->store_id = $storeId;
                }
            }
        });
    }

    /**
     * Scope to filter results by the current or provided store.
     */
    public function scopeOfStore($query, $storeId = null)
    {
        $storeId = $storeId ?? static::resolveStoreId();
        if ($storeId) {
            $query->where($this->getTable() . '.store_id', $storeId);
        }
        return $query;
    }

    /**
     * Resolve current store_id — flexible for future session or middleware-based access.
     */
    protected static function resolveStoreId(): ?int
    {
        if (session()->has('store_id')) {
            return session('store_id');
        }
        // // 1️⃣ Try Auth user

        // if (app()->has('current_store')) {
        //     return app('current_store')->id;
        // }

        return null;
    }
}
