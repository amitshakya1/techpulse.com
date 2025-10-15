<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends BaseModel
{
    use Sluggable;

    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
