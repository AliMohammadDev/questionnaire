<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectAdditionalQuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'additional_questions' => ['present', 'array', 'list'],
            'additional_questions.*' => ['array:question,answer'],
            'additional_questions.*.question' => ['required', 'string', 'max:2000'],
            'additional_questions.*.answer' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
