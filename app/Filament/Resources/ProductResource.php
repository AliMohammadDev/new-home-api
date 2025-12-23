<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\ProductsCountWidget;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;
  protected static ?string $navigationIcon = 'heroicon-o-cube';
  protected static ?int $navigationSort = 1;
  protected static ?string $navigationLabel = 'منتجات';
  protected static ?string $pluralModelLabel = 'منتجات';
  protected static ?string $modelLabel = 'منتج';
  protected static ?string $navigationGroup = 'إدارة المنتجات';


  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->label('اسم المنتج')
          ->required(),

        Forms\Components\Textarea::make('body')
          ->label('الوصف')
          ->required(),

        Forms\Components\Select::make('category_id')
          ->label('الصنف')
          ->relationship('category', 'name')
          ->required(),

        Forms\Components\TextInput::make('price')
          ->label('السعر')
          ->numeric()
          ->required(),

        Forms\Components\TextInput::make('discount')
          ->label('الخصم %')
          ->numeric()
          ->default(0),

        Forms\Components\Toggle::make('is_featured')
          ->label('منتج مميز'),

        SpatieMediaLibraryFileUpload::make('image')
          ->label('الصورة')
          ->collection('product_images')
          ->image()
          ->imageEditor()
          ->maxSize(10240)
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
          ->collection('product_images')
          ->conversion('default')
          ->label('الصورة'),

        Tables\Columns\TextColumn::make('name')
          ->label('الاسم')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('category.name')
          ->label('الصنف')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('price')
          ->label('السعر')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('final_price')
          ->label('السعر النهائي')
          ->sortable()
          ->searchable(),

        Tables\Columns\IconColumn::make('is_featured')
          ->label('مميز')
          ->boolean()
          ->sortable()
          ->searchable(),
      ])
      ->filters([
        Tables\Filters\Filter::make('name')
          ->label('بحث بالاسم')
          ->form([
            Forms\Components\TextInput::make('name'),
          ])
          ->query(
            fn(Builder $query, array $data) =>
            $query->when($data['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%$name%"))
          ),
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
      'index' => Pages\ListProducts::route('/'),
      'create' => Pages\CreateProduct::route('/create'),
      'edit' => Pages\EditProduct::route('/{record}/edit'),
      'view' => Pages\ViewProduct::route('/{record}'),

    ];
  }




}