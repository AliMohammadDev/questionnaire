<?php

namespace App\Http\Resources;

use App\Models\Question;
use App\Models\QuestionCondition;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Question */
class QuestionnaireQuestionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'placeholder' => $this->placeholder,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order,
            'validation_rules' => $this->validation_rules,
            'section' => [
                'id' => $this->questionSection->id,
                'name' => $this->questionSection->name,
                'description' => $this->questionSection->description,
                'sort_order' => $this->questionSection->sort_order,
            ],
            'answer_type' => [
                'id' => $this->answerType->id,
                'name' => $this->answerType->name,
                'code' => $this->answerType->code,
                'has_options' => $this->answerType->has_options,
                'allows_multiple' => $this->answerType->allows_multiple,
            ],
            'options' => $this->options->map(fn (QuestionOption $option): array => [
                'id' => $option->id,
                'label' => $option->label,
                'image' => $option->getFirstMedia('image')?->getUrl(),
                'value' => $option->value,
                'sort_order' => $option->sort_order,
            ])->all(),
            'conditions' => $this->conditions->map(fn (QuestionCondition $condition): array => [
                'depends_on_question_id' => $condition->depends_on_question_id,
                'depends_on_question' => $condition->dependsOnQuestion ? [
                    'id' => $condition->dependsOnQuestion->id,
                    'title' => $condition->dependsOnQuestion->title,
                ] : null,
                'operator' => $condition->operator,
                'question_option_id' => $condition->question_option_id,
                'value' => $condition->value,
                'logic_type' => $condition->logic_type,
            ])->all(),
        ];
    }
}
