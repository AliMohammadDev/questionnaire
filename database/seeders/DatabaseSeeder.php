<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            ProjectCategorySeeder::class,
            ProjectTypeSeeder::class,
            DesignStyleSeeder::class,
            QuestionSectionSeeder::class,
            AnswerTypeSeeder::class,
            QuestionSeeder::class,
            QuestionOptionSeeder::class,
            QuestionContextSeeder::class,
            QuestionConditionSeeder::class,
            ClientSeeder::class,
            ProjectSeeder::class,
            AnswerSeeder::class,
            AnswerOptionSeeder::class,
            MediaSeeder::class,
        ]);
    }
}
