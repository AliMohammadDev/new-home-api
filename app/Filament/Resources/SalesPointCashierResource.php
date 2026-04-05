<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesPointCashierResource\Pages;
use App\Filament\Resources\SalesPointCashierResource\RelationManagers\FatoraRelationManager;
use App\Models\SalesPointCashier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class SalesPointCashierResource extends Resource
{
  protected static ?string $model = SalesPointCashier::class;
  protected static ?string $navigationGroup = 'نقاط البيع (POS)';
  protected static ?string $navigationLabel = 'الكاشيرات';
  protected static ?string $navigationIcon = 'heroicon-o-calculator';
  protected static ?string $pluralModelLabel = ' الكاشيرات';
  protected static ?string $modelLabel = 'كاشير جديد';


  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('بيانات الكاشير الأساسية')
        ->schema([
          Forms\Components\Select::make('sales_point_id')
            ->label('نقطة البيع')
            ->relationship(
              name: 'salesPoint',
              titleAttribute: 'name',
              modifyQueryUsing: fn(Builder $query) =>
              auth()->user()->hasRole('super_admin')
              ? $query
              : $query->whereHas('managers', fn($q) => $q->where('user_id', auth()->id()))
            )
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('user_id')
            ->label('المستخدم (الكاشير)')
            ->relationship('user', 'name')
            ->searchable()
            ->preload()
            ->required(),

          Forms\Components\Select::make('shift_type')
            ->label('نوع الوردية')
            ->options([
              'morning' => 'صباحية',
              'evening' => 'مسائية',
              'night' => 'ليلية',
              'full_time' => 'دوام كامل',
            ])
            ->native(false)
            ->required(),



        ])->columns(2),
    ]);
  }
  public static function table(Table $table): Table
  {

    return $table
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('الكاشير')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('salesPoint.name')
          ->label('نقطة البيع')
          ->searchable()
          ->badge(),

        Tables\Columns\TextColumn::make('daily_limit')
          ->label('حد الصندوق')
          ->money('USD', locale: 'en_US')
        ,

        Tables\Columns\TextColumn::make('shift_type')
          ->label('الوردية')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'morning' => 'warning',
            'evening' => 'info',
            'night' => 'primary',
            'full_time' => 'success',
            default => 'gray',
          })
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'morning' => 'صباحية',
            'evening' => 'مسائية',
            'night' => 'ليلية',
            'full_time' => 'دوام كامل',
            default => $state,
          }),


      ])
      ->filters([
        Tables\Filters\SelectFilter::make('sales_point_id')
          ->label('نقطة البيع')
          ->relationship('salesPoint', 'name')
          ->searchable()
          ->preload(),

        Tables\Filters\SelectFilter::make('shift_type')
          ->label('نوع الوردية')
          ->options([
            'morning' => 'صباحية',
            'evening' => 'مسائية',
            'night' => 'ليلية',
            'full_time' => 'دوام كامل',
          ]),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->before(function (Tables\Actions\DeleteAction $action, SalesPointCashier $record) {
            if ($record->fatora()->exists()) {
              \Filament\Notifications\Notification::make()
                ->danger()
                ->title('لا يمكن حذف الكاشير')
                ->body('هذا الكاشير لديه فواتير مسجلة في النظام.')
                ->send();
              $action->halt();
            }

            if ($record->transactions()->exists()) {
              \Filament\Notifications\Notification::make()
                ->danger()
                ->title('لا يمكن حذف الكاشير')
                ->body('هذا الكاشير مرتبط بسجلات تحويلات مالية (مناقلات).')
                ->send();
              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      FatoraRelationManager::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListSalesPointCashiers::route('/'),
      'create' => Pages\CreateSalesPointCashier::route('/create'),
      'edit' => Pages\EditSalesPointCashier::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery()->with(['user', 'salesPoint']);
    $user = auth()->user();

    if ($user->hasRole('super_admin')) {
      return $query;
    }

    if ($user->hasRole('sales_point_manager')) {
      return $query->whereHas('salesPoint.managers', function (Builder $subQuery) use ($user) {
        $subQuery->where('user_id', $user->id);
      });
    }

    if ($user->hasRole('sales_point_cashier')) {
      return $query->where('user_id', $user->id);
    }

    return $query->whereRaw('1 = 0');

  }
}