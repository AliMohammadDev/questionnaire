<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuestionConditionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['pool-area' => 'pool', 'domain-name' => 'has-domain'] as $target => $source) {
            QuestionSeeder::question($target)->conditions()->firstOrCreate(['depends_on_question_id' => QuestionSeeder::question($source)->id, 'operator' => 'equals', 'question_option_id' => null, 'value' => '1', 'logic_type' => 'AND']);
        }
    }
}
