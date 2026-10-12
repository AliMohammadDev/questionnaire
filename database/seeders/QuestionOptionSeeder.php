<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuestionOptionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (QuestionSeeder::definitions() as $key => $data) {
            $question = QuestionSeeder::question($key);
            foreach ($data['options'] as $value => $label) {
                $question->options()->firstOrCreate(['value' => $value], ['label' => $label, 'is_active' => true, 'sort_order' => array_search($value, array_keys($data['options']), true)]);
            }
        }
    }
}
