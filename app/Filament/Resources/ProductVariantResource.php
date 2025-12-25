<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantResource extends Resource
{
  protected static ?string $model = ProductVariant::class;
  protected static ?string $navigationIcon = 'heroicon-o-tag';
  protected static ?string $navigationLabel = 'خيارات المنتج';
  protected static ?string $navigationGroup = 'إدارة المنتجات';
  protected static ?string $modelLabel = 'خيار المنتج';
  protected static ?string $pluralModelLabel = 'خيارات المنتج';



  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('product_id')
          ->relationship('product', 'name')
          ->label('المنتج')
          ->required(),

        Forms\Components\Select::make('color_id')
          ->relationship('color', 'color')
          ->label('اللون')
          ->required(),

        Forms\Components\Select::make('size_id')
          ->relationship('size', 'size')
          ->label('الحجم')
          ->required(),

        Forms\Components\Select::make('material_id')
          ->relationship('material', 'material')
          ->label('المادة')
          ->required(),

        Forms\Components\TextInput::make('stock_quantity')
          ->label('الكمية')
          ->numeric()
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('product.name')->label('المنتج')->sortable()
          ->searchable(),
        TextColumn::make('color.color')->label('اللون')->sortable()->searchable(),
        TextColumn::make('size.size')->label('الحجم')->sortable()->searchable(),
        TextColumn::make('material.material')->label('المادة')->sortable()->searchable(),
        TextColumn::make('stock_quantity')->label('الكمية')->sortable()->searchable(),
      ])
      ->filters([])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),

      ])
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make(),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('product')
          ->label('المنتج')
          ->relationship('product', 'name'),

        Tables\Filters\SelectFilter::make('color')
          ->label('اللون')
          ->relationship('color', 'color'),

        Tables\Filters\SelectFilter::make('size')
          ->label('الحجم')
          ->relationship('size', 'size'),

        Tables\Filters\SelectFilter::make('material')
          ->label('المادة')
          ->relationship('material', 'material'),

        Tables\Filters\Filter::make('stock_quantity')
          ->label('الكمية')
          ->form([
            Forms\Components\TextInput::make('min')->numeric()->label('أقل كمية'),
            Forms\Components\TextInput::make('max')->numeric()->label('أعلى كمية'),
          ])
          ->query(function (Builder $query, array $data) {
            if (isset($data['min'])) {
              $query->where('stock_quantity', '>=', $data['min']);
            }
            if (isset($data['max'])) {
              $query->where('stock_quantity', '<=', $data['max']);
            }
          }),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),

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
      'index' => Pages\ListProductVariants::route('/'),
      'create' => Pages\CreateProductVariant::route('/create'),
      'edit' => Pages\EditProductVariant::route('/{record}/edit'),
      'view' => Pages\ViewProductVariant::route('/{record}'),
    ];
  }
}
