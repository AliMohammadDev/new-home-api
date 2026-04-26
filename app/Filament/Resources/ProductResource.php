<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;
  protected static ?int $navigationSort = 1;
  protected static ?string $navigationIcon = 'heroicon-o-cube';
  protected static ?string $navigationLabel = 'منتجات';
  protected static ?string $pluralModelLabel = 'منتجات';
  protected static ?string $modelLabel = 'منتج';
  protected static ?string $navigationGroup = 'إدارة المنتجات';



  /**
   * Get a new instance of the model's query builder.
   *
   * @return \Illuminate\Database\Eloquent\Builder
   */
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
            Forms\Components\Tabs\Tab::make('English')->schema([
              Section::make()
                ->schema([
                  TextInput::make('name.en')
                    ->label('اسم المنتج (EN)')
                    ->required(),
                  Textarea::make('body.en')
                    ->label('الوصف (EN)')
                    ->required(),
                ]),
            ]),
            Forms\Components\Tabs\Tab::make('Arabic')->schema([
              Section::make()
                ->schema([
                  TextInput::make('name.ar')
                    ->label('اسم المنتج (AR)')
                    ->required(),
                  Textarea::make('body.ar')
                    ->label('الوصف (AR)')
                    ->required(),
                ]),
            ]),
          ])->columnSpanFull(),

        Section::make('معلومات إضافية')
          ->schema([
            Select::make('category_id')
              ->label('الصنف')
              ->relationship('category', 'id')
              ->getOptionLabelFromRecordUsing(fn($record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
              ->searchable(['name'])
              ->preload()
              ->required(),
            Toggle::make('is_featured')
              ->label('منتج مميز'),
          ])->columns(2),
      ]);
  }


  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')
          ->label('الاسم')
          ->getStateUsing(fn(Product $record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->sortable()
          ->searchable(query: function ($query, string $search) {
            $query->where('name->' . App::getLocale(), 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%");
          }),

        TextColumn::make('body')
          ->label('الوصف')
          ->getStateUsing(fn(Product $record) => $record->body[App::getLocale()] ?? $record->body['en'] ?? '')
          ->limit(50)
          ->sortable()
          ->searchable(query: function ($query, string $search) {
            $query->where('body->' . App::getLocale(), 'like', "%{$search}%")
              ->orWhere('body->en', 'like', "%{$search}%");
          }),

        TextColumn::make('category.name')
          ->label('الصنف')
          ->getStateUsing(fn($record) => $record->category->name[App::getLocale()] ?? $record->category->name['en'] ?? '')
          ->sortable(),

        IconColumn::make('is_featured')
          ->label('مميز')
          ->boolean()
          ->sortable()
          ->searchable(),
      ])
      ->filters([
        Filter::make('name')
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

        SelectFilter::make('category_id')
          ->label('الصنف')
          ->relationship('category', 'id')
          ->getOptionLabelFromRecordUsing(fn($record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->searchable()
          ->preload(),

        TrashedFilter::make()
          ->label('حالة الأرشفة')
          ->native(false),
      ])
      ->defaultSort('created_at', 'DESC')
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
