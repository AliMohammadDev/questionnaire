<?php

namespace App\Providers;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DeleteAction::configureUsing(fn (DeleteAction $action): DeleteAction => $action
            ->authorizationNotification()
            ->modalDescription(function (Model $record): string {
                if ($record instanceof Media) {
                    return 'سيتم حذف المرفق وملفه نهائياً من التخزين. هل تريد المتابعة؟';
                }

                if ($record instanceof HasMedia && $record->media()->exists()) {
                    return 'سيتم حذف السجل وجميع ملفاته وصوره المرفقة نهائياً. هل تريد المتابعة؟';
                }

                if ($record instanceof Role) {
                    return 'سيتم حذف الدور وروابط صلاحياته نهائياً. لن تُحذف الصلاحيات نفسها. هل تريد المتابعة؟';
                }

                return 'هل تريد حذف هذا السجل نهائياً؟ لن يتم حذف السجلات المرتبطة به تلقائياً؛ وجودها يمنع الحذف.';
            })
            ->successNotificationTitle('تم الحذف بنجاح'));

        DeleteBulkAction::configureUsing(fn (DeleteBulkAction $action): DeleteBulkAction => $action
            ->label('حذف المحدد')
            ->modalHeading('حذف السجلات المحددة')
            ->modalSubmitActionLabel('تأكيد حذف المحدد')
            ->modalDescription('سيتم حذف السجلات المحددة المسموح بحذفها نهائياً مع صورها ومرفقاتها وروابط صلاحياتها إن وجدت. السجلات المرتبطة ببيانات أخرى لن تُحذف، وسيظهر إشعار يوضح النتيجة.')
            ->authorizeIndividualRecords('delete')
            ->successNotificationTitle('تم حذف السجلات المحددة بنجاح')
            ->failureNotificationTitle(fn (int $successCount, int $totalCount): string => 'تم حذف '.$successCount.' من أصل '.$totalCount.' سجلاً محدداً.')
            ->missingBulkAuthorizationFailureNotificationMessage(fn (int $failureCount): string => 'تعذّر حذف '.$failureCount.' سجلاً بسبب بيانات مرتبطة أو عدم امتلاك صلاحية الحذف.')
            ->missingBulkProcessingFailureNotificationMessage(fn (int $failureCount): string => 'تعذّر حذف '.$failureCount.' سجلاً. أعد المحاولة أو راجع البيانات المرتبطة.'));

        FilamentAsset::register([
            Css::make('questionnaire-dashboard', resource_path('css/dashboard.css')),
        ]);
    }
}
