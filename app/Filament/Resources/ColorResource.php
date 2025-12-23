<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColorResource\Pages;
use App\Filament\Resources\ColorResource\RelationManagers;
use App\Models\Color;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class ColorResource extends Resource
{
  protected static ?string $model = Color::class;
  protected static ?string $navigationIcon = 'heroicon-o-swatch';
  protected static ?string $navigationLabel = 'الألوان';
  protected static ?string $pluralModelLabel = 'الألوان';
  protected static ?string $modelLabel = 'لون';
  protected static ?string $navigationGroup = 'إدارة المنتجات';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('color')
          ->label('اسم اللون')
          ->required(),

        ColorPicker::make('hex_code')
          ->label('كود اللون')
          ->required()
          ->format('hex'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ColorColumn::make('hex_code')
          ->label('اللون')
          ->copyable(),

        Tables\Columns\TextColumn::make('color')
          ->label('اللون')
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
            Forms\Components\TextInput::make('color')->label('اللون'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['color'] ?? null, fn($q, $color) => $q->where('color', 'like', "%$color%"));
          }),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),
        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, Color $record) {
            if ($record->productVariants()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذا اللون لارتباطه بمنتجات.')
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
      'index' => Pages\ListColors::route('/'),
      'create' => Pages\CreateColor::route('/create'),
      'edit' => Pages\EditColor::route('/{record}/edit'),
      'view' => Pages\ViewColor::route('/{record}'),
    ];
  }
}
