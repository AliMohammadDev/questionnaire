<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends ShieldRoleResource
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الدور')
                ->description('أنشئ دورًا مثل «مدير المشاريع» أو «مراجع»، ثم حدد ما يستطيع مستخدمو هذا الدور الوصول إليه وتنفيذه.')
                ->schema([
                    TextInput::make('name')->label('اسم الدور')->placeholder('مثال: مراجع المشاريع')
                        ->required()->maxLength(255)->unique(ignoreRecord: true),
                    Hidden::make('guard_name')->default('web'),
                    static::getSelectAllFormComponent()->label('منح جميع الصلاحيات')
                        ->helperText('يحدد جميع صلاحيات الصفحات والعمليات لهذا الدور. يمكنك أيضًا اختيار الصلاحيات لكل صفحة أدناه.'),
                ])->columns(2)->columnSpanFull(),
            Tabs::make('صلاحيات الدور')->tabs([
                Tab::make('صفحات الإدارة')->schema(static::getResourceEntitiesSchema()),
                Tab::make('الصفحة الرئيسية')->schema([
                    Section::make('الوصول إلى الصفحة الرئيسية')
                        ->description('فعّل هذه الصلاحية ليتمكن المستخدم من فتح الصفحة الرئيسية بعد تسجيل الدخول.')
                        ->schema([
                            static::getCheckboxListFormComponent('pages_tab', static::getPageOptions())
                                ->default(['View:Dashboard']),
                        ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(FilamentShield::getResources())->map(function (array $entity): Section {
            $resource = $entity['resourceFqcn'];

            return Section::make($resource::getPluralModelLabel())
                ->description('عرض القائمة يتيح فتح الصفحة، وعرض التفاصيل يتيح فتح سجل محدد. اختر بقية العمليات حسب مسؤوليات الدور.')
                ->schema([static::getCheckBoxListComponentForResource($entity)])
                ->collapsible()->columnSpanFull();
        })->values()->all();
    }

    public static function getCheckBoxListComponentForResource(array $entity): Component
    {
        $descriptions = [];
        foreach (array_keys(static::getResourcePermissionOptions($entity)) as $permission) {
            $descriptions[$permission] = match (strstr($permission, ':', true)) {
                'ViewAny' => 'فتح الصفحة وعرض قائمة السجلات.',
                'View' => 'فتح تفاصيل سجل محدد.',
                'Create' => 'إضافة سجلات جديدة.',
                'Update' => 'تعديل بيانات السجلات الموجودة.',
                'Delete' => 'حذف سجل مع مراعاة قواعد الحذف والبيانات المرتبطة.',
                'DeleteAny' => 'استخدام الحذف الجماعي عندما يكون متاحًا.',
                'Restore' => 'استرجاع سجل محذوف عندما تدعم الصفحة ذلك.',
                'RestoreAny' => 'استرجاع عدة سجلات محذوفة عندما يكون متاحًا.',
                'ForceDelete' => 'حذف سجل نهائيًا عندما يكون متاحًا.',
                'ForceDeleteAny' => 'الحذف النهائي الجماعي عندما يكون متاحًا.',
                'Replicate' => 'إنشاء نسخة من سجل عندما يكون متاحًا.',
                'Reorder' => 'تغيير ترتيب السجلات عندما يكون متاحًا.',
                default => '',
            };
        }

        return parent::getCheckBoxListComponentForResource($entity)
            ->label('العمليات المسموحة')->columns(['default' => 1, 'md' => 2, 'xl' => 3])
            ->descriptions($descriptions);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('اسم الدور')->searchable()->sortable(),
            TextColumn::make('users_count')->label('عدد المستخدمين')->counts('users')->badge()->color('info'),
            TextColumn::make('permissions_count')->label('عدد الصلاحيات')->counts('permissions')->badge(),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime('Y/m/d H:i')->sortable(),
        ])->recordActions([
            ViewAction::make()->label('تفاصيل الدور'),
            EditAction::make()->label('تعديل الصلاحيات'),
            DeleteAction::make(),
        ])->defaultSort('name')->striped()->paginated([10, 25, 50])
            ->emptyStateHeading('لا توجد أدوار بعد')
            ->emptyStateDescription('أضف دورًا جديدًا وحدد صلاحياته، ثم أسنده للمستخدمين من صفحة المستخدمين.');
    }

    public static function infolist(Schema $schema): Schema
    {
        $sections = [
            Section::make('ملخص الدور')->schema([
                TextEntry::make('name')->label('اسم الدور'),
                TextEntry::make('permissions_count')->label('عدد الصلاحيات')
                    ->state(fn (Role $record): int => $record->permissions->count()),
                TextEntry::make('users.name')->label('المستخدمون المسند إليهم الدور')->badge()->placeholder('لم يسند إلى مستخدمين بعد'),
            ])->columns(2)->columnSpanFull(),
        ];
        foreach (FilamentShield::getResources() as $entity) {
            $options = static::getResourcePermissionOptions($entity);
            $sections[] = Section::make($entity['resourceFqcn']::getPluralModelLabel())->schema([
                TextEntry::make($entity['resourceFqcn'])->label('الصلاحيات الممنوحة')
                    ->state(fn (Role $record): array => $record->permissions->pluck('name')
                        ->filter(fn (string $permission): bool => isset($options[$permission]))
                        ->map(fn (string $permission): string => $options[$permission])->values()->all())
                    ->badge()->placeholder('لا توجد صلاحيات لهذه الصفحة'),
            ])->collapsible()->columnSpanFull();
        }
        $sections[] = Section::make('الصفحة الرئيسية')->schema([
            TextEntry::make('dashboard_access')->label('الوصول إلى الصفحة الرئيسية')
                ->state(fn (Role $record): string => $record->hasPermissionTo('View:Dashboard') ? 'مسموح' : 'غير مسموح'),
        ])->columnSpanFull();

        return $schema->components($sections);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
