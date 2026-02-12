<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;
  protected static ?int $navigationSort = 1;
  protected static ?string $navigationIcon = 'heroicon-o-cube';
  protected static ?string $navigationLabel = 'منتجات';
  protected static ?string $pluralModelLabel = 'منتجات';
  protected static ?string $modelLabel = 'منتج';
  protected static ?string $navigationGroup = 'إدارة المنتجات';


  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        \Illuminate\Database\Eloquent\SoftDeletingScope::class,
      ]);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Tabs::make('Languages')
          ->tabs([
            Tabs\Tab::make('English')->schema([
              Forms\Components\Card::make()
                ->schema([
                  Forms\Components\TextInput::make('name.en')
                    ->label('اسم المنتج (EN)')
                    ->required(),
                  Forms\Components\Textarea::make('body.en')
                    ->label('الوصف (EN)')
                    ->required(),
                ]),
            ]),
            Tabs\Tab::make('Arabic')->schema([
              Forms\Components\Card::make()
                ->schema([
                  Forms\Components\TextInput::make('name.ar')
                    ->label('اسم المنتج (AR)')
                    ->required(),
                  Forms\Components\Textarea::make('body.ar')
                    ->label('الوصف (AR)')
                    ->required(),
                ]),
            ]),
          ]),

        Forms\Components\Card::make()
          ->schema([
            Forms\Components\Select::make('category_id')
              ->label('الصنف')
              ->relationship('category', 'id')
              ->getOptionLabelFromRecordUsing(fn($record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
              ->searchable(['name'])
              ->preload()
              ->required(),
            Forms\Components\Toggle::make('is_featured')
              ->label('منتج مميز'),


          ]),
      ]);
  }



  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('الاسم')
          ->getStateUsing(fn(Product $record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->sortable()
          ->searchable(query: function ($query, string $search) {
            $query->where('name->' . App::getLocale(), 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%");
          }),

        Tables\Columns\TextColumn::make('body')
          ->label('الوصف')
          ->getStateUsing(fn(Product $record) => $record->body[App::getLocale()] ?? $record->body['en'] ?? '')
          ->limit(50)
          ->sortable()
          ->searchable(query: function ($query, string $search) {
            $query->where('body->' . App::getLocale(), 'like', "%{$search}%")
              ->orWhere('body->en', 'like', "%{$search}%");
          }),

        Tables\Columns\TextColumn::make('category.name')
          ->label('الصنف')
          ->getStateUsing(fn($record) => $record->category->name[App::getLocale()] ?? $record->category->name['en'] ?? '')
          ->sortable(),

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
            Forms\Components\TextInput::make('name_search')->label('اسم المنتج'),
          ])
          ->query(function ($query, array $data) {
            return $query->when($data['name_search'], function ($q, $name) {
              $locale = App::getLocale();
              return $q->where("name->{$locale}", 'like', "%{$name}%")
                ->orWhere("name->en", 'like', "%{$name}%");
            });
          }),

        Tables\Filters\SelectFilter::make('category_id')
          ->label('الصنف')
          ->relationship('category', 'id')
          ->getOptionLabelFromRecordUsing(fn($record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->searchable()
          ->preload(),

        Tables\Filters\TrashedFilter::make()
          ->label('حالة الأرشفة')
          ->native(false),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),
        Tables\Actions\DeleteAction::make()->label('أرشفة'),
        Tables\Actions\RestoreAction::make()->label('استعادة'),

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
