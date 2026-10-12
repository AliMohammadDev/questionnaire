<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class AnswerOptionSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = QuestionSeeder::definitions();
        foreach (ProjectSeeder::scenarios() as $code => $scenario) {
            $project = Project::with('answers.answerOptions')->where('project_code', $code)->sole();
            foreach (AnswerSeeder::demoAnswers($scenario) as $key => $values) {
                if (! in_array($definitions[$key]['type'], ['single_choice', 'multiple_choice', 'dropdown'], true)) {
                    continue;
                }
                $question = QuestionSeeder::question($key);
                $answer = $project->answers->firstWhere('question_id', $question->id);
                if (! $answer) {
                    continue;
                }
                $optionIds = [];
                foreach ($values as $value) {
                    $optionIds[] = $question->options()->where('value', $value)->sole()->id;
                }
                $answer->options()->sync($optionIds);
            }
        }
    }
}
