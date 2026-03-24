<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use App\Models\Size;
use Filament\Forms;
use Filament\Tables;

class SizeResource extends Resource
{
  protected static ?string $model = Size::class;
  protected static ?int $navigationSort = 3;
  protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';
  protected static ?string $navigationLabel = 'الأحجام';
  protected static ?string $pluralModelLabel = 'الأحجام';
  protected static ?string $modelLabel = 'حجم';
  protected static ?string $navigationGroup = 'إدارة المنتجات';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('size')
          ->label('اسم الحجم')
          ->required()

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('size')
          ->label('الحجم')
          ->searchable()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\Filter::make('color')
          ->label('بحث باللون')
          ->form([
            Forms\Components\TextInput::make('size')->label('الحجم'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['size'] ?? null, fn($q, $color) => $q->where('size', 'like', "%$color%"));
          }),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),

        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, Size $record) {
            if ($record->productVariants()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذا الحجم لارتباطه بمنتجات.')
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
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListSizes::route('/'),
      'create' => Pages\CreateSize::route('/create'),
      'edit' => Pages\EditSize::route('/{record}/edit'),
      'view' => Pages\ViewSize::route('/{record}'),

    ];
  }
}
