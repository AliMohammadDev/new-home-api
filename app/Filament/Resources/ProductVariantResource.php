<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        Forms\Components\Section::make('تعديل بيانات الخيار')
          ->visible(fn($context) => in_array($context, ['edit', 'view']))
          ->schema([
            Forms\Components\Grid::make(3)
              ->schema([
                Forms\Components\Select::make('color_id')
                  ->label('اللون')
                  ->options(\App\Models\Color::pluck('color', 'id'))
                  ->required()
                ,
                Forms\Components\Select::make('size_id')
                  ->label('الحجم')
                  ->options(\App\Models\Size::pluck('size', 'id'))
                  ->required(),
                Forms\Components\Select::make('material_id')
                  ->label('المادة')
                  ->options(\App\Models\Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required()
                ,
                Forms\Components\Grid::make(2)
                  ->schema([
                    Forms\Components\TextInput::make('price')->label('السعر'),
                    Forms\Components\TextInput::make('discount')->label('الخصم %'),
                  ]),
              ]),
            Forms\Components\TextInput::make('stock_quantity')
              ->label('الكمية الحالية')
              ->numeric()
              ->required(),

            Forms\Components\Repeater::make('images')
              ->relationship('images')
              ->label('صور الخيار')
              ->schema([
                FileUpload::make('image')
                  ->label('الصورة')
                  ->image()
                  ->multiple()
                  ->maxFiles(1)
                  ->directory('product_variants')
                  ->visibility('public')
                  ->required()
                  ->getUploadedFileNameForStorageUsing(function ($file) {
                    return (string) Str::uuid() . '.webp';
                  })
                  ->imageEditor()
                  ->formatStateUsing(function ($state) {
                    if (blank($state))
                      return [];
                    $path = is_array($state) ? $state : [$state];
                    return collect($path)->map(function ($p) {
                      return str_contains($p, 'product_variants/') ? $p : "product_variants/{$p}";
                    })->toArray();
                  })
                  ->dehydrateStateUsing(function ($state) {
                    if (blank($state))
                      return null;
                    $path = is_array($state) ? (array_values($state)[0] ?? null) : $state;
                    return $path ? basename((string) $path) : null;
                  }),
              ])
              ->saveRelationshipsUsing(function ($record, $state) {
                $existingImages = $record->images()->pluck('image', 'id')->toArray();
                $newItems = collect($state);

                $newIds = $newItems->pluck('id')->filter()->toArray();
                $deletedIds = array_diff(array_keys($existingImages), $newIds);

                foreach ($deletedIds as $id) {
                  $imageName = $existingImages[$id];
                  if ($imageName) {
                    $path = 'product_variants/' . $imageName;
                    if (Storage::disk('public')->exists($path)) {
                      Storage::disk('public')->delete($path);
                    }
                  }
                  ProductVariantImage::where('id', $id)->delete();
                }

                foreach ($state as $item) {
                  $imageValue = $item['image'] ?? null;
                  if (is_array($imageValue)) {
                    $imageValue = array_values($imageValue)[0] ?? null;
                  }

                  if ($imageValue) {
                    $imageValue = basename((string) $imageValue);
                  }

                  if (isset($item['id']) && $item['id']) {
                    ProductVariantImage::where('id', $item['id'])->update([
                      'image' => $imageValue
                    ]);
                  } else {
                    $record->images()->create([
                      'image' => $imageValue
                    ]);
                  }
                }
              })
              ->grid(3)
              ->columnSpanFull(),
          ]),

        // Auto create
        Forms\Components\Section::make('أدوات التوليد السريع')
          ->visible(fn($context) => $context === 'create')
          ->schema([
            Forms\Components\Grid::make(3)
              ->schema([
                Forms\Components\MultiSelect::make('temp_colors')
                  ->label('الألوان')
                  ->options(\App\Models\Color::pluck('color', 'id'))
                  ->dehydrated(false),
                Forms\Components\MultiSelect::make('temp_sizes')
                  ->label('الأحجام')
                  ->options(\App\Models\Size::pluck('size', 'id'))
                  ->dehydrated(false),
                Forms\Components\MultiSelect::make('temp_materials')
                  ->label('المواد')
                  ->options(\App\Models\Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->dehydrated(false),
              ]),
            Forms\Components\Actions::make([
              Forms\Components\Actions\Action::make('generate_variants')
                ->label('توليد الخيارات تلقائياً')
                ->icon('heroicon-m-sparkles')
                ->color('success')
                ->action(function ($get, $set) {
                  $colors = $get('temp_colors') ?? [];
                  $sizes = $get('temp_sizes') ?? [];
                  $materials = $get('temp_materials') ?? [];
                  if (empty($colors) || empty($sizes) || empty($materials))
                    return;

                  $variants = [];
                  foreach ($colors as $colorId) {
                    foreach ($sizes as $sizeId) {
                      foreach ($materials as $materialId) {
                        $variants[] = [
                          'color_id' => $colorId,
                          'size_id' => $sizeId,
                          'material_id' => $materialId,
                          'stock_quantity' => 1,
                          'price' => 0,
                          'discount' => 0,
                        ];
                      }
                    }
                  }
                  $set('variants', $variants);
                }),
            ]),
          ]),

        Forms\Components\Select::make('product_id')
          ->label('المنتج')
          ->relationship('product', 'id')
          ->getOptionLabelFromRecordUsing(fn($record) => $record->name[app()->getLocale()] ?? $record->name['en'] ?? '')
          ->required()
          ->visible(fn($context) => $context === 'create')
          ->columnSpanFull(),

        Forms\Components\Repeater::make('variants')
          ->label('قائمة الخيارات الناتجة')
          ->visible(fn($context) => $context === 'create')
          ->schema([
            Forms\Components\Grid::make(4)
              ->schema([
                Forms\Components\Select::make('color_id')
                  ->label('اللون')
                  ->options(\App\Models\Color::pluck('color', 'id'))->required(),
                Forms\Components\Select::make('size_id')
                  ->label('الحجم')
                  ->options(\App\Models\Size::pluck('size', 'id'))->required(),
                Forms\Components\Select::make('material_id')
                  ->label('المادة')
                  ->options(\App\Models\Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required(),
                Forms\Components\TextInput::make('stock_quantity')
                  ->label('الكمية')
                  ->numeric()->required(),
                Forms\Components\TextInput::make('price')->label('السعر')->numeric()
                  ->required()
                  ->minValue(0.01)
                ,
                Forms\Components\TextInput::make('discount')->label('الخصم %')->numeric()->default(0),
              ]),
            FileUpload::make('images')
              ->label('صور الخيار')
              ->multiple()
              ->image()
              ->directory('product_variants')
          ])
          ->columnSpanFull(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('images.image')
          ->label('الصورة')
          ->circular()
          ->stacked()
          ->limit(3)
          ->getStateUsing(function ($record) {
            return $record->images->map(function ($img) {
              $path = $img->image;
              return str_contains($path, 'product_variants/') ? $path : 'product_variants/' . $path;
            })->toArray();
          }),

        Tables\Columns\TextColumn::make('product.name')
          ->label('المنتج')
          ->getStateUsing(function (ProductVariant $record) {
            $name = $record->product?->name;
            if (is_array($name)) {
              return $name[app()->getLocale()] ?? $name['en'] ?? '';
            }
            return $name ?? '';
          })
          ->sortable()
          ->searchable(),
        Tables\Columns\ColorColumn::make('color.hex_code')
          ->label('اللون')
          ->sortable(),
        TextColumn::make('size.size')->label('الحجم')->sortable()->searchable(),
        TextColumn::make('material.material')->label('المادة')->sortable()->searchable(),
        TextColumn::make('stock_quantity')->label('الكمية')->sortable()->searchable(),
        Tables\Columns\TextColumn::make('price')
          ->label('السعر')
          ->sortable()
          ->searchable(),

        Tables\Columns\TextColumn::make('final_price')
          ->label('السعر النهائي')
          ->sortable(),
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
    return [];
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
