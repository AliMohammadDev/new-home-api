<?php

namespace App\Filament\Resources\SalesPointResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ManagersRelationManager extends RelationManager
{
  // ملاحظة: تأكد أن العلاقة في موديل SalesPoint هي belongsToMany
  // وتستخدم using(SalesPointManager::class) إذا أردت التعامل مع الموديل الوسيط بدقة
  protected static string $relationship = 'managers';
  protected static ?string $title = 'الإدارة المشرفة';
  protected static ?string $modelLabel = 'مدير مشرف';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('user_id')
          ->label('اختر المستخدم')
          ->relationship('user', 'name')
          ->searchable()
          ->preload()
          ->required(),

        Forms\Components\TextInput::make('phone')
          ->label('رقم هاتف العمل (في النقطة)')
          ->tel()
          ->placeholder('أدخل رقم التواصل الخاص بالمدير في هذه النقطة'),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
      ->recordUrl(
        fn(\App\Models\User $record): string =>
        "/admin/sales-point-managers/{$record->pivot->id}/edit"
      )
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('الاسم الكامل')
          ->icon('heroicon-m-user-circle')
          ->weight('bold')
          ->searchable(),

        Tables\Columns\TextColumn::make('email')
          ->label('البريد الإلكتروني')
          ->color('gray')
          ->copyable(),

        Tables\Columns\TextColumn::make('pivot.phone')
          ->label('هاتف النقطة')
          ->icon('heroicon-m-phone')
          ->color('primary')
          ->copyable()
          ->default('غير محدد'),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ التعيين')
          ->dateTime('Y-m-d')
          ->sortable(),
      ])
      ->filters([])
      ->headerActions([
        Tables\Actions\AttachAction::make()
          ->form(fn(Tables\Actions\AttachAction $action): array => [
            $action->getRecordSelect(),
            Forms\Components\TextInput::make('phone')
              ->label('رقم هاتف العمل')
              ->tel(),
          ])
          ->preloadRecordSelect(),
      ])
      ->actions([

      ]);
  }
}