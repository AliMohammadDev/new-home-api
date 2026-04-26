<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointManagerResource\Pages;
use App\Filament\Resources\SalesPointManagerResource\RelationManagers\SalesPointRelationManager;
use App\Models\SalesPointManager;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SalesPointManagerResource extends Resource
{
  protected static ?string $model = SalesPointManager::class;
  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationGroup = 'نقاط البيع (POS)';
  protected static ?string $navigationLabel = 'مدراء النقاط';
  protected static ?string $pluralModelLabel = 'مدراء نقاط البيع';
  protected static ?string $modelLabel = 'تعيين مدير';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('ربط مدير بنقطة بيع')
          ->schema([
            Select::make('user_id')
              ->label('المستخدم (مدير نقطة البيع)')
              ->relationship('user', 'name')
              ->searchable()
              ->preload()
              ->required(),

            Select::make('sales_point_id')
              ->label('نقطة البيع')
              ->relationship('salesPoint', 'name')
              ->searchable()
              ->preload()
              ->required(),

            TextInput::make('phone')
              ->label('رقم الهاتف')
              ->tel()
              ->required(),

          ])->columns(2),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([

        TextColumn::make('user.name')
          ->label('اسم المدير')
          ->sortable()
          ->searchable()
          ->icon('heroicon-m-user'),

        TextColumn::make('user.email')
          ->label('البريد الإلكتروني')
          ->color('gray'),


        TextColumn::make('phone')
          ->url(fn($state) => "tel:{$state}")
          ->label('الهاتف')
          ->formatStateUsing(fn(string $state): string => "📞 " . $state)
          ->extraAttributes(['class' => 'font-mono']),

        TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->sortable()
          ->searchable()
          ->badge()
          ->color('info'),

        TextColumn::make('created_at')
          ->label('تاريخ التعيين')
          ->dateTime('d/m/Y')
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        SelectFilter::make('sales_point_id')
          ->label('تصفية حسب النقطة')
          ->relationship('salesPoint', 'name'),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        EditAction::make(),
        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, SalesPointManager $record) {
            if ($record->cashierTransactions()->exists()) {
              Notification::make()
                ->danger()
                ->title('لا يمكن حذف المدير')
                ->body('هذا المدير مسؤول عن تحويلات مالية سابقة للكاشيرات. حذفه سيؤدي إلى فقدان سجلات المحاسبة.')
                ->persistent()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make(),
      ]);
  }



  public static function getRelations(): array
  {
    return [
      SalesPointRelationManager::class
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListSalesPointManagers::route('/'),
      'create' => Pages\CreateSalesPointManager::route('/create'),
      'edit' => Pages\EditSalesPointManager::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()->with(['user', 'salesPoint']);

    if (auth()->user()->hasRole('super_admin')) {
      return $query;
    }

    return $query->where('user_id', auth()->id());
  }
}