<?php

namespace App\Filament\Resources\QuestionContexts\Schemas;

use App\Filament\Support\QuestionnaireFields;
use App\Models\DesignStyle;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class QuestionContextForm
{
  public static function configureCreate(Schema $schema): Schema
  {
    return $schema->components([
      Section::make('البيانات الأساسية')->description('اختر عدة تصنيفات وأنواع وأنماط لإضافة سياقات للسؤال دفعة واحدة. اترك المستوى فارغاً ليشمل جميع قيمه.')->schema([
        QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال'),
        QuestionnaireFields::department(false)->afterStateUpdated(function (Set $set): void {
          $set('project_category_ids', []);
          $set('project_type_ids', []);
        }),
        Select::make('project_category_ids')->label('تصنيفات المشروع')->multiple()->searchable()->preload()
          ->options(fn(Get $get): array => ProjectCategory::query()->when($get('department_id'), fn(Builder $query, mixed $department): Builder => $query->where('department_id', $department))->orderBy('sort_order')->pluck('name', 'id')->all())
          ->in(fn(Select $component): array => array_keys($component->getOptions()))
          ->live()->afterStateUpdated(fn(Set $set) => $set('project_type_ids', [])),
        Select::make('project_type_ids')->label('أنواع المشروع')->multiple()->searchable()->preload()
          ->options(fn(Get $get): array => ProjectType::query()
            ->when($get('project_category_ids'), fn(Builder $query, array $categories): Builder => $query->whereIn('project_category_id', $categories))
            ->when($get('department_id'), fn(Builder $query, mixed $department): Builder => $query->whereHas('projectCategory', fn(Builder $query): Builder => $query->where('department_id', $department)))
            ->orderBy('sort_order')->pluck('name', 'id')->all())
          ->in(fn(Select $component): array => array_keys($component->getOptions())),
        Select::make('design_style_ids')->label('الأنماط التصميمية')->multiple()->searchable()->preload()
          ->options(fn(): array => DesignStyle::query()->orderBy('sort_order')->pluck('name', 'id')->all())
          ->in(fn(Select $component): array => array_keys($component->getOptions())),
      ])->columns(2)->columnSpanFull(),
    ]);
  }

  public static function configure(Schema $schema): Schema
  {
    return $schema->components([
      Section::make('البيانات الأساسية')->description('اترك أي مستوى فارغاً ليشمل جميع قيمه. يجب أن تتوافق الاختيارات مع هيكل المشروع.')->schema([
        QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال'),
        QuestionnaireFields::department(false),
        QuestionnaireFields::category(false),
        QuestionnaireFields::projectType(false),
        QuestionnaireFields::relationship('design_style_id', 'designStyle', 'name', 'النمط التصميمي')->required(false),
      ])->columns(2)->columnSpanFull(),
    ]);
  }
}
