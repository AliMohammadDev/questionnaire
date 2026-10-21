<?php

namespace App\Filament\Support;

use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Exists;

class QuestionnaireFields
{
  public static function relationship(string $name, string $relationship, string $title, string $label): Select
  {
    return Select::make($name)->label($label)->relationship($relationship, $title)
      ->searchable()->preload()->native(false)->required();
  }

  public static function department(bool $required = true): Select
  {
    return self::relationship('department_id', 'department', 'name', 'القسم الرئيسي')
      ->required($required)->live()->afterStateUpdated(function (Set $set): void {
        $set('project_category_id', null);
        $set('project_type_id', null);
      });
  }

  public static function category(bool $required = true): Select
  {
    return self::relationship('project_category_id', 'projectCategory', 'name', 'تصنيف المشروع')
      ->required($required)->relationship('projectCategory', 'name', modifyQueryUsing: function (Builder $query, Get $get): Builder {
        return $query->when($get('department_id'), fn(Builder $query, mixed $department): Builder => $query->where('department_id', $department));
      })
      ->exists(table: ProjectCategory::class, column: 'id', modifyRuleUsing: fn(Exists $rule, Get $get): Exists => $get('department_id') ? $rule->where('department_id', $get('department_id')) : $rule)
      ->live();
  }

  public static function projectType(bool $required = true): Select
  {
    return self::relationship('project_type_id', 'projectType', 'name', 'نوع المشروع')
      ->required($required)->relationship('projectType', 'name', modifyQueryUsing: function (Builder $query, Get $get): Builder {
        return $query->when($get('department_id'), fn(Builder $query, mixed $department): Builder => $query->whereHas('projectCategory', fn(Builder $query): Builder => $query->where('department_id', $department)));
      })
      ->rules([fn(Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
        $type = ProjectType::with('projectCategory')->find($value);
        if ($type && ($get('department_id') && $type->projectCategory->department_id != $get('department_id'))) {
          $fail('نوع المشروع لا يتبع القسم الرئيسي المحدد.');
        }
      }]);
  }

  public static function answerCode(Get $get): ?string
  {
    return Question::with('answerType')->find($get('question_id'))?->answerType?->code;
  }
}
