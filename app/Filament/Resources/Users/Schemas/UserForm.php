<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                TextInput::make('name')->label('الاسم')->maxLength(255)->required(),
                TextInput::make('email')->label('البريد الإلكتروني')->maxLength(255)->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')->label('كلمة المرور')->password()->revealable()->afterStateHydrated(fn (TextInput $component) => $component->state(null))->minLength(12)->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn (?string $state): bool => filled($state))->helperText('اتركها فارغة عند التعديل للاحتفاظ بكلمة المرور.'),
            ])->columns(2)->columnSpanFull(),
            Section::make('الأدوار والصلاحيات')
                ->description('حدد الأدوار التي تمنح المستخدم الوصول للصفحات والعمليات المطلوبة. المستخدم دون أدوار أو صلاحيات لا يمكنه دخول اللوحة.')
                ->visible(fn (): bool => auth()->user()->can('Update:Role'))
                ->schema([
                    Select::make('roles')->label('الأدوار')
                        ->relationship('roles', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('guard_name', 'web'))
                        ->multiple()->preload()->searchable(),
                    Select::make('permissions')->label('صلاحيات إضافية')
                        ->relationship('permissions', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('guard_name', 'web'))
                        ->multiple()->preload()->searchable()
                        ->helperText('تضاف إلى الصلاحيات الممنوحة من الأدوار.'),
                ])->columns(2)->columnSpanFull(),
        ]);
    }
}
