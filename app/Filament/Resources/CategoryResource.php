<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\App;

class CategoryResource extends Resource
{
  protected static ?string $model = Category::class;
  protected static ?int $navigationSort = 6;
  protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
  protected static ?string $navigationLabel = 'أصناف';
  protected static ?string $pluralModelLabel = 'أصناف';
  protected static ?string $modelLabel = 'صنف';
  protected static ?string $navigationGroup = 'إدارة المنتجات';



  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Tabs::make('Languages')
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
                Forms\Components\TextInput::make('name.ar')
                  ->label('اسم التصنيف (AR)')
                  ->required(),
                Forms\Components\Textarea::make('description.ar')
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
        Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
          ->collection('category_images')
          ->limit(3)
          ->conversion('default')
          ->label('الصورة')
          ->circular(),

        Tables\Columns\TextColumn::make('name')
          ->label('الاسم')
          ->getStateUsing(fn(Category $record) => $record->name[App::getLocale()] ?? $record->name['en'] ?? '')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('description')
          ->label('الوصف')
          ->getStateUsing(fn(Category $record) => $record->description[App::getLocale()] ?? $record->description['en'] ?? '')
          ->sortable()
          ->searchable()
          ->limit(50),

        Tables\Columns\TextColumn::make('created_at')
          ->label('تاريخ الإنشاء')
          ->sortable()
          ->searchable()
          ->date(),
      ])
      ->defaultSort('created_at', 'desc')
      ->filters([
        Tables\Filters\Filter::make('name')
          ->label('اسم التصنيف')
          ->form([
            Forms\Components\TextInput::make('name')->label('اسم التصنيف'),
          ])
          ->query(function (Builder $query, array $data) {
            return $query->when($data['name'] ?? null, function ($q, $name) {
              $locale = App::getLocale();
              $q->where("name->$locale", 'like', "%$name%");
            });
          }),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\ViewAction::make()->label('عرض'),
        Tables\Actions\DeleteAction::make()
          ->label('حذف')
          ->before(function (Tables\Actions\DeleteAction $action, Category $record) {
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
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),

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
