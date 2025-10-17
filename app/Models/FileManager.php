<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileManager extends BaseModel implements HasMedia
{
    use Sluggable, InteractsWithMedia;

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

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('images')
            ->useDisk('s3') // or 'public' disk
            ->singleFile(); // optional, remove if multiple files allowed
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->format('webp')
            ->performOnCollections('images');

        $this
            ->addMediaConversion('medium')
            ->width(600)
            ->height(400)
            ->format('webp')
            ->performOnCollections('images');

        $this
            ->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->format('webp')
            ->performOnCollections('images');
    }
}
