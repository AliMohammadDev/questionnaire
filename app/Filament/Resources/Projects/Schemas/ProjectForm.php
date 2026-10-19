<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Support\QuestionnaireFields;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ProjectForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema->components(self::components());
  }

  /** @return array<Component> */
  public static function components(): array
  {
    return [
      Section::make('البيانات الأساسية')->schema([
        QuestionnaireFields::relationship('client_id', 'client', 'name', 'العميل')
          ->createOptionForm(fn(Schema $schema): Schema => ClientForm::configure($schema))
          ->createOptionUsing(function (array $data): int {
            Gate::authorize('create', Client::class);

            return Client::create($data)->id;
          })
          ->createOptionAction(fn(Action $action): Action => $action->label('إضافة عميل')->modalHeading('إضافة عميل')->visible(fn(): bool => ClientResource::canCreate())),
        TextInput::make('project_code')->label('كود المشروع')->maxLength(100)->required()->unique(ignoreRecord: true)->default(fn(): string => 'PRJ-' . Str::upper(Str::random(10))),
        TextInput::make('project_name')->label('اسم المشروع')->maxLength(255),
        QuestionnaireFields::department(),
        QuestionnaireFields::category(),
        QuestionnaireFields::projectType()->live(),
        QuestionnaireFields::relationship('design_style_id', 'designStyle', 'name', 'النمط التصميمي')->required(false)->live(),
        DateTimePicker::make('submitted_at')->label('تاريخ الإرسال')->native(false),
        DateTimePicker::make('completed_at')->label('تاريخ الإكمال')->native(false),
        Textarea::make('notes')->label('الملاحظات')->rows(3)->columnSpanFull(),
      ])->columns(2)->columnSpanFull(),
      Section::make('الأسئلة الإضافية')->schema([
        Repeater::make('additional_questions')->label('أسئلة خاصة بالمشروع')
          ->schema([
            Textarea::make('question')->label('السؤال')->required()->maxLength(2000)->rows(2),
            Textarea::make('answer')->label('الجواب')->maxLength(65535)->rows(3),
          ])
          ->defaultItems(0)->addActionLabel('إضافة سؤال')->columns(2)->columnSpanFull(),
      ])->columnSpanFull(),
      Section::make('الصور والمرفقات')->schema([
        SpatieMediaLibraryFileUpload::make('attachments')->label('مرفقات المشروع')->collection('attachments')->disk('public')->maxSize(10240)->multiple()->reorderable()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->downloadable()->openable()->columnSpanFull(),
      ])->columns(2)->columnSpanFull(),
    ];
  }
}
