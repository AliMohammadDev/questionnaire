<?php

namespace App\Support;

use App\Models\Answer;
use App\Models\Project;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaPathGenerator extends DefaultPathGenerator
{
    public function getPath(Media $media): string
    {
        return $media->getCustomProperty('storage_path') ?? parent::getPath($media);
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive-images/';
    }

    public function namedPath(Media $media): string
    {
        $owner = $media->model;
        $project = $owner instanceof Project ? $owner : ($owner instanceof Answer ? $owner->project : null);
        $record = $project ?? $owner;
        $name = $project ? ($project->project_name ?: $project->project_code) : ($record?->name ?? $record?->label ?? 'ملفات');
        $name = trim(preg_replace('/[^\p{L}\p{N}\s_-]+/u', '-', $name) ?? '', " .-\t\n\r");
        $name = mb_substr($name ?: 'ملفات', 0, 100);
        $bucket = $project ? 'projects' : Str::kebab(Str::pluralStudly(class_basename($record ?? $media)));
        $prefix = trim(config('media-library.prefix', ''), '/');

        return ($prefix !== '' ? $prefix.'/' : '').$bucket.'/'.$name.'-'.($record?->getKey() ?? $media->model_id).'/'.$media->getKey().'/';
    }
}
