<?php

namespace App\Models;

use App\Support\MediaPathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class Media extends SpatieMedia
{
    protected static function booted(): void
    {
        static::created(function (self $media): void {
            $media->setCustomProperty('storage_path', app(MediaPathGenerator::class)->namedPath($media));
            $media->saveQuietly();
        });
    }
}
