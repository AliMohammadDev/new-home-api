<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\App;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;


class CategoryResource extends Resource
{
  protected static ?string $model = Category::class;
  protected static ?int $navigationSort = 1;
  protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
  protected static ?string $navigationLabel = 'أصناف';
  protected static ?string $pluralModelLabel = 'أصناف';
  protected static ?string $modelLabel = 'صنف';
  protected static ?string $navigationGroup = 'إدارة المنتجات';



  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Tabs::make('Languages')
          ->tabs([
            Forms\Components\Tabs\Tab::make('English')
              ->schema([
                Forms\Components\TextInput::make('name.en')
                  ->label('اسم التصنيف (EN)')
                  ->required(),
                Forms\Components\Textarea::make('description.en')
                  ->label('الوصف (EN)'),
              ]),
            Forms\Components\Tabs\Tab::make('Arabic')
              ->schema([
                TextInput::make('name.ar')
                  ->label('اسم التصنيف (AR)')
                  ->required(),
                Textarea::make('description.ar')
                  ->label('الوصف (AR)'),
              ]),
          ]),

        SpatieMediaLibraryFileUpload::make('image')
          ->label('الصورة')
          ->collection('category_images')
          ->multiple()
          ->image()
          ->imageEditor()
          ->maxSize(10240),
      ]);
  }


  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        SpatieMediaLibraryImageColumn::make('image')
          ->collection('category_images')
          ->limit(3)
          ->conversion('default')
          ->label('الصورة')
          ->circular(),
        TextColumn::make('name')
          ->label('الاسم')
          ->getStateUsing(fn(Category $record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->sortable()
          ->searchable(query: function (Builder $query, string $search): Builder {
            $locale = App::getLocale();
            return $query->where("name->$locale", 'like', "%{$search}%")
              ->orWhere("name->en", 'like', "%{$search}%");
          }),

        TextColumn::make('description')
          ->label('الوصف')
          ->getStateUsing(fn(Category $record) => $record->description[App::getLocale()] ?? $record->description['en'] ?? '')
          ->sortable()
          ->searchable()
          ->limit(50),

        TextColumn::make('products_count')
          ->counts('products')
          ->label('عدد المنتجات'),

        TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        Filter::make('name')
          ->label('اسم التصنيف')
          ->form([
            TextInput::make('name')->label('اسم التصنيف'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['name'], function ($q, $name) {
              $locale = App::getLocale();
              return $q->where(function ($subQuery) use ($name, $locale) {
                $subQuery->where("name->$locale", 'like', "%$name%")
                  ->orWhere("name->en", 'like', "%$name%");
              });
            });
          }),
      ])
      ->actions([
        EditAction::make(),
        ViewAction::make()->label('عرض'),
        DeleteAction::make()
          ->label('حذف')
          ->before(function (DeleteAction $action, Category $record) {
            if ($record->products()->exists()) {
              Notification::make()
                ->danger()
                ->title('خطأ في الحذف')
                ->body('لا يمكن حذف هذا الصنف لارتباطه بمنتج.')
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
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCategories::route('/'),
      'create' => Pages\CreateCategory::route('/create'),
      'edit' => Pages\EditCategory::route('/{record}/edit'),
      'view' => Pages\ViewCategory::route('/{record}'),
    ];
  }

}
