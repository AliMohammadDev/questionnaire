<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Project;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DepartmentSeeder::catalog() as $departmentKey => $departmentData) {
            $department = DepartmentSeeder::department($departmentKey);
            $this->addExampleImage($department, 'gallery', $department->sort_order);
            $this->addExampleImage($department, 'gallery', $department->sort_order + 1, 1);
            foreach (array_keys($departmentData['categories']) as $categoryKey) {
                $category = ProjectCategorySeeder::category($departmentKey, $categoryKey);
                $this->addExampleImage($category, 'gallery', $category->sort_order);
                $this->addExampleImage($category, 'gallery', $category->sort_order + 1, 1);
            }
        }
        foreach (array_keys(DesignStyleSeeder::catalog()) as $index => $styleKey) {
            $style = DesignStyleSeeder::style($styleKey);
            $this->addExampleImage($style, 'cover', $index);
            $this->addExampleImage($style, 'gallery', $index + 2);
            $this->addExampleImage($style, 'gallery', $index + 3, 1);
        }
        foreach (array_values(ProjectSeeder::scenarios()) as $index => $scenario) {
            $type = ProjectTypeSeeder::type($scenario['department'], $scenario['category'], $scenario['type']);
            $this->addExampleImage($type, 'image', $index);
            $this->addExampleImage($type, 'image', $index + 1, 1);
        }
        foreach (QuestionOption::orderBy('id')->get() as $index => $option) {
            $this->addExampleImage($option, 'image', $index);
        }
        foreach (array_keys(ProjectSeeder::scenarios()) as $index => $code) {
            $project = Project::where('project_code', $code)->sole();
            $this->addExampleImage($project, 'attachments', $index);
            $answer = $project->answers()->where('question_id', QuestionSeeder::question('references')->id)->first();
            if ($answer) {
                $this->addExampleImage($answer, 'attachments', $index);
            }
        }
    }

    private function addExampleImage(HasMedia $model, string $collection, int $index, int $slot = 0): void
    {
        if ($model->hasMedia($collection)) {
            $existing = $model->getMedia($collection);
            if ($existing->contains(fn (Media $media): bool => ! $media->getCustomProperty('demo'))
                || $existing->contains(fn (Media $media): bool => (int) $media->getCustomProperty('seed_image_slot', 0) === $slot)) {
                return;
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'questionnaire-seed-');
        if ($path === false) {
            throw new \RuntimeException('Unable to create a temporary seed image.');
        }
        try {
            $image = imagecreatetruecolor(640, 400);
            $palettes = [[124, 58, 237], [14, 116, 144], [180, 83, 9], [75, 85, 99], [5, 150, 105]];
            [$red, $green, $blue] = $palettes[$index % count($palettes)];
            $background = imagecolorallocate($image, 245, 243, 255);
            $accent = imagecolorallocate($image, $red, $green, $blue);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $background);
            imagefilledrectangle($image, 55, 60, 585, 340, $white);
            imagefilledrectangle($image, 85, 90, 320, 310, $accent);
            imagefilledrectangle($image, 350, 100, 550, 135, $accent);
            imagefilledrectangle($image, 350, 170, 510, 195, $background);
            imagefilledrectangle($image, 350, 220, 550, 245, $background);
            imagepng($image, $path);
            imagedestroy($image);
            $model->addMedia($path)->usingName('صورة مرجعية تجريبية — ليست صورة مشروع حقيقي')
                ->usingFileName($slot === 0 ? 'demo-reference.png' : 'demo-reference-'.($slot + 1).'.png')
                ->withCustomProperties(['demo' => true, 'language' => 'ar', 'seed_image_slot' => $slot])
                ->toMediaCollection($collection, 'public');
            $model->unsetRelation('media');
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
