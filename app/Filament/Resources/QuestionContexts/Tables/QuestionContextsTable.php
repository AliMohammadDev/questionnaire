<?php

namespace App\Filament\Resources\QuestionContexts\Tables;

use App\Filament\Support\QuestionnaireFields;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionContextsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('question.title')->label('السؤال')->searchable()->limit(50),
            TextColumn::make('department.name')->label('القسم')->placeholder('جميع الأقسام'),
            TextColumn::make('projectCategory.name')->label('التصنيف')->placeholder('جميع التصنيفات'),
            TextColumn::make('projectType.name')->label('نوع المشروع')->placeholder('جميع الأنواع'),
            TextColumn::make('designStyle.name')->label('النمط')->placeholder('جميع الأنماط'),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y/m/d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            Filter::make('context')->label('سياق السؤال')->schema([
                QuestionnaireFields::department(false),
                QuestionnaireFields::category(false),
                QuestionnaireFields::projectType(false),
                QuestionnaireFields::relationship('design_style_id', 'designStyle', 'name', 'النمط')->required(false),
            ])->columns(4)->columnSpanFull()
                ->query(function (Builder $query, array $data): Builder {
                    foreach (['department_id', 'project_category_id', 'project_type_id', 'design_style_id'] as $column) {
                        if (filled($data[$column] ?? null)) {
                            $query->where($column, $data[$column]);
                        }
                    }

                    return $query;
                })
                ->indicateUsing(fn (array $data): ?string => collect($data)->contains(fn (mixed $value): bool => filled($value)) ? 'سياق السؤال' : null),
        ], layout: FiltersLayout::AboveContent)->recordActions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->toolbarActions([DeleteBulkAction::make()])
            ->defaultSort('id', 'desc')
            ->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد سجلات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول سجل من زر الإضافة أعلى الصفحة.');
    }
}
