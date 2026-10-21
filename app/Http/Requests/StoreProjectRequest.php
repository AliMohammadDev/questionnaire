<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', Rule::exists(Client::class, 'id')],
            'department_id' => ['required', 'integer', Rule::exists(Department::class, 'id')],
            'project_category_id' => ['required', 'integer', Rule::exists(ProjectCategory::class, 'id')->where('department_id', $this->input('department_id'))],
            'project_type_id' => ['required', 'integer', Rule::exists(ProjectType::class, 'id')->where(fn (Builder $query): Builder => $query->whereIn('project_category_id', ProjectCategory::query()->select('id')->where('department_id', $this->input('department_id'))))],
            'design_style_id' => ['nullable', 'integer', Rule::exists(DesignStyle::class, 'id')],
            'project_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'additional_questions' => ['sometimes', 'array', 'list'],
            'additional_questions.*' => ['array:question,answer,notes'],
            'additional_questions.*.question' => ['required', 'string', 'max:2000'],
            'additional_questions.*.answer' => ['nullable', 'string', 'max:65535'],
            'additional_questions.*.notes' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
