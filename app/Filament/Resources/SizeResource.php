<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeResource\Pages;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Form;
use App\Models\Size;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

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
        TextInput::make('size')
          ->label('اسم الحجم')
          ->required()
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('size')
          ->label('الحجم')
          ->searchable()
          ->sortable()
          ->searchable(),
        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Filter::make('color')
          ->label('بحث باللون')
          ->form([
            TextInput::make('size')
              ->label('الحجم'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['size'] ?? null, fn($q, $color) => $q->where('size', 'like', "%$color%"));
          }),
      ])
      ->defaultSort('created_at', 'DESC')
      ->actions([
        EditAction::make(),
        ViewAction::make()->label('عرض'),

        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, Size $record) {
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
        BulkActionGroup::make([
          DeleteBulkAction::make(),
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
