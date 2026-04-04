<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyEntryResource\Pages;
use App\Models\CompanyEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompanyEntryResource extends Resource
{
  protected static ?string $model = CompanyEntry::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
  protected static ?string $navigationGroup = 'الإدارة المالية';
  protected static ?string $navigationLabel = 'حركات الصناديق';
  protected static ?string $pluralModelLabel = 'سجل حركات الصناديق';
  protected static ?string $modelLabel = 'حركة مالية';
  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('تفاصيل الحركة المالية')
          ->schema([
            Forms\Components\Select::make('company_treasure_id')
              ->label('الصندوق')
              ->relationship('treasure', 'name')
              ->required()
              ->searchable()
              ->preload(),

            Forms\Components\Select::make('user_id')
              ->label('الموظف المسؤول')
              ->relationship('user', 'name')
              ->default(auth()->id())
              ->required()
              ->searchable(),

            Forms\Components\Select::make('trans_type')
              ->label('نوع العملية')
              ->options([
                'deposit' => 'دائن',
                'withdraw' => 'مدين',
              ])
              ->required()
              ->native(false),

            Forms\Components\TextInput::make('amount')
              ->label('المبلغ')
              ->numeric()
              ->required()
              ->prefix('USD'),

            Forms\Components\TextInput::make('name')
              ->label('البيان / السبب')
              ->required()
              ->columnSpanFull()
              ->placeholder('مثال: دفعة من مورد، مصاريف نثرية...'),
          ])->columns(2),
      ]);
  }
  public static function table(Table $table): Table
  {
    return $table->columns([
      Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime(),
      Tables\Columns\TextColumn::make('treasure.name')
        ->label('الصندوق')
        ->searchable()
        ->sortable(),

      Tables\Columns\TextColumn::make('user.name')
        ->label('الموظف')
        ->searchable()
        ->sortable(),

      Tables\Columns\TextColumn::make('trans_type')
        ->label('نوع العملية')
        ->badge()
        ->formatStateUsing(fn(string $state): string => match ($state) {
          'deposit' => 'دائن',
          'withdraw' => 'مدين',
          default => $state,
        })
        ->color(fn(string $state): string => match ($state) {
          'deposit' => 'success',
          'withdraw' => 'danger',
          default => 'gray',
        })
        ->icon(fn(string $state): string => match ($state) {
          'deposit' => 'heroicon-m-arrow-trending-up',
          'withdraw' => 'heroicon-m-arrow-trending-down',
          default => 'heroicon-m-minus',
        }),


      Tables\Columns\TextColumn::make('name')
        ->label('البيان')
        ->searchable(),
      Tables\Columns\TextColumn::make('amount')
        ->label('المبلغ')
        ->money('USD', locale: 'en_US')
        ->summarize([
          Tables\Columns\Summarizers\Sum::make()
            ->label('الإجمالي')
            ->money('USD', locale: 'en_US'),
        ]),
    ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Tables\Filters\SelectFilter::make('trans_type')
          ->label('نوع الحركة')
          ->options([
            'deposit' => 'دائن',
            'withdraw' => 'مدين',
          ]),
        Tables\Filters\SelectFilter::make('company_treasure_id')
          ->label('الصندوق')
          ->relationship('treasure', 'name'),

        Tables\Filters\TrashedFilter::make()
          ->label('حالة السجلات')
          ->trueLabel('السجلات المؤرشفة فقط')
          ->falseLabel('السجلات النشطة فقط')
          ->placeholder('الكل')
          ->native(false),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          ->label('أرشفة'),
        Tables\Actions\RestoreAction::make()
          ->label('استعادة'),
        Tables\Actions\ForceDeleteAction::make()
          ->label('حذف نهائي')
          ->before(function (Tables\Actions\ForceDeleteAction $action, $record) {
            if ($record->amount != 0) {
              Notification::make()
                ->title('غير مسموح')
                ->body('يجب تصفير المبلغ أولاً قبل الحذف النهائي.')
                ->warning()
                ->send();

              $action->halt();
            }
          }),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->label('أرشفة المحدد'),
          Tables\Actions\RestoreBulkAction::make()
            ->label('استعادة المحدد'),
          Tables\Actions\ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد')
            ->before(function (Tables\Actions\ForceDeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
              $invalidRecords = $records->where('amount', '!=', 0);

              if ($invalidRecords->count() > 0) {
                Notification::make()
                  ->title('لا يمكن الحذف النهائي')
                  ->body('بعض السجلات المختارة تحتوي على مبالغ غير صفرية. يجب تصفير المبالغ أولاً.')
                  ->danger()
                  ->send();

                $action->halt();
              }
            }),
        ]),
      ]);

  }
  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withTrashed()
      ->with(['treasure', 'user']);
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCompanyEntries::route('/'),
      'create' => Pages\CreateCompanyEntry::route('/create'),
      'edit' => Pages\EditCompanyEntry::route('/{record}/edit'),
    ];
  }
}
