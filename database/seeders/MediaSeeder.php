<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Project;
use App\Support\SeedPhotoCatalog;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DepartmentSeeder::catalog() as $departmentKey => $departmentData) {
            $department = DepartmentSeeder::department($departmentKey);
            $photos = match ($departmentKey) {
                'architecture' => ['architecture', 'buildings'],
                'graphic-design' => ['graphic', 'branding'],
                'web-design' => ['web', 'services'],
            };
            foreach ($photos as $slot => $photo) {
                $this->addPhoto($department, 'gallery', $photo, $slot);
            }
            foreach (array_keys($departmentData['categories']) as $categoryKey) {
                $category = ProjectCategorySeeder::category($departmentKey, $categoryKey);
                $photos = $this->categoryPhotos($categoryKey);
                foreach ($photos as $slot => $photo) {
                    $this->addPhoto($category, 'gallery', $photo, $slot);
                }
                foreach (array_keys($departmentData['categories'][$categoryKey]['types']) as $typeKey) {
                    $type = ProjectTypeSeeder::type($departmentKey, $categoryKey, $typeKey);
                    $typePhotos = $this->typePhotos($typeKey, $categoryKey);
                    foreach ($typePhotos as $slot => $photo) {
                        $this->addPhoto($type, 'image', $photo, $slot);
                    }
                }
            }
        }
        foreach (array_keys(DesignStyleSeeder::catalog()) as $styleKey) {
            $style = DesignStyleSeeder::style($styleKey);
            $photo = match ($styleKey) {
                'neoclassical' => 'classic',
                'contemporary' => 'modern',
                'corporate' => 'office',
                'playful' => 'colorful',
                default => $styleKey,
            };
            $this->addPhoto($style, 'cover', $photo);
            $this->addPhoto($style, 'gallery', $photo);
            $this->addPhoto($style, 'gallery', $styleKey === 'playful' ? 'graphic' : ($styleKey === 'corporate' ? 'services' : $photo), 1);
        }
        foreach (QuestionSeeder::definitions() as $key => $definition) {
            foreach (QuestionSeeder::question($key)->options()->get() as $option) {
                $photo = $definition['option_images'][$option->value] ?? null;
                if ($photo) {
                    $this->addPhoto($option, 'image', $photo);
                } else {
                    foreach ($option->getMedia('image') as $media) {
                        if ($media->getCustomProperty('demo')) {
                            $media->delete();
                        }
                    }
                }
            }
        }
        foreach (ProjectSeeder::scenarios() as $code => $scenario) {
            $project = Project::where('project_code', $code)->sole();
            $photo = $this->typePhotos($scenario['type'], $scenario['category'])[0];
            $this->addPhoto($project, 'attachments', $photo);
            $answer = $project->answers()->where('question_id', QuestionSeeder::question('references')->id)->first();
            if ($answer) {
                $this->addPhoto($answer, 'attachments', $photo);
            }
        }
    }

    /** @return array{string, string} */
    private function categoryPhotos(string $key): array
    {
        return match ($key) {
            'residential' => ['architecture', 'residential'],
            'commercial' => ['commercial', 'office'],
            'industrial' => ['industrial', 'buildings'],
            'branding' => ['branding', 'graphic'],
            'print-design' => ['printing', 'graphic'],
            'digital-marketing' => ['marketing', 'graphic'],
            'corporate-websites' => ['web', 'services'],
            'e-commerce' => ['ecommerce', 'web'],
            'service-platforms' => ['services', 'web'],
        };
    }

    /** @return array{string, string} */
    private function typePhotos(string $type, string $category): array
    {
        return match ($type) {
            'villa' => ['architecture', 'residential'],
            'apartment' => ['residential', 'modern'],
            'residential-complex', 'office-building' => ['buildings', 'office'],
            'restaurant', 'retail-store' => ['commercial', 'office'],
            default => $this->categoryPhotos($category),
        };
    }

    private function addPhoto(HasMedia $model, string $collection, string $photo, int $slot = 0): void
    {
        $existing = $model->getMedia($collection);
        if ($existing->contains(fn (Media $media): bool => ! $media->getCustomProperty('demo'))) {
            return;
        }
        $previous = $existing->first(fn (Media $media): bool => (int) $media->getCustomProperty('seed_image_slot', 0) === $slot);
        $source = SeedPhotoCatalog::sourceUrl($photo);
        if ($previous?->getCustomProperty('source_url') === $source) {
            return;
        }
        $model->addMedia(SeedPhotoCatalog::path($photo))->preservingOriginal()
            ->usingName('صورة مرجعية — '.$photo)
            ->usingFileName('reference-'.$photo.'-'.($slot + 1).'.jpg')
            ->withCustomProperties(['demo' => true, 'seed_image_slot' => $slot, 'source_url' => $source, 'source' => 'Pexels', 'license_url' => 'https://www.pexels.com/license/'])
            ->toMediaCollection($collection, 'public');
        $previous?->delete();
        $model->unsetRelation('media');
    }
}
