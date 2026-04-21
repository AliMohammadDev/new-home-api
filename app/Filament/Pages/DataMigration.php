<?php

namespace App\Filament\Pages;

use App\Jobs\ArchiveDataJob;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class DataMigration extends Page
{
  use HasPageShield;

  protected static ?string $navigationIcon = 'heroicon-o-archive-box';
  protected static ?string $navigationGroup = 'الإعدادات المتقدمة';
  protected static ?string $navigationLabel = 'ترحيل وأرشفة المواد';
  protected static ?string $title = 'ترحيل البيانات الحالي';

  protected static string $view = 'filament.pages.data-migration';

  protected function getHeaderActions(): array
  {
    return [
      Action::make('run_migration')
        ->label('ترحيل كافة البيانات الآن')
        ->icon('heroicon-o-archive-box')
        ->color('warning')
        ->size('xl')
        ->requiresConfirmation()
        ->modalHeading('تأكيد الترحيل الكامل')
        ->modalDescription('عند التأكيد، سيتم أرشفة كافة السجلات الموجودة حالياً في النظام وحتى هذه اللحظة. ستظهر الجداول فارغة بعد العملية. هل أنت متأكد؟')
        ->modalSubmitActionLabel('نعم، أرشفة الكل')
        ->action(fn() => $this->performMigration()),
    ];
  }

  public function performMigration()
  {
    try {
      ArchiveDataJob::dispatch(auth()->user());

      Notification::make()
        ->title('بدأت عملية الأرشفة في الخلفية')
        ->warning()
        ->body('يتم الآن معالجة البيانات، ستتلقى إشعاراً عند اكتمال العملية بنجاح.')
        ->persistent()
        ->send();

    } catch (\Exception $e) {
      Notification::make()
        ->title('خطأ في بدء العملية')
        ->danger()
        ->body('حدث خطأ: ' . $e->getMessage())
        ->send();
    }
  }

}