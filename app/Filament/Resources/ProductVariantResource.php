<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Filament\Resources\Resource;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Forms;

class ProductVariantResource extends Resource
{
  protected static ?string $model = ProductVariant::class;
  protected static ?int $navigationSort = 5;
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
            Forms\Components\Grid::make(4)
              ->schema([
                Forms\Components\Select::make('color_id')
                  ->label('اللون')
                  ->options(\App\Models\Color::pluck('color', 'id'))
                  ->required(),
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
                  ->required(),

                Forms\Components\TextInput::make('sku')
                  ->label('رمز الـ SKU / الباركود')
                  ->placeholder('مثال: SHIRT-RED-L')
                  ->unique(ignoreRecord: true)
                  ->required()
                  ->helperText('هذا الكود سيُستخدم لتوليد الباركود البصري'),

                Forms\Components\Grid::make(4)
                  ->schema([
                    Forms\Components\TextInput::make('price')->label('السعر'),
                    Forms\Components\TextInput::make('discount')->label('الخصم %'),
                    Forms\Components\TextInput::make('stock_quantity')
                      ->label('الكمية الحالية')
                      ->numeric()
                      ->required(),

                    Forms\Components\Select::make('product_import_id')
                      ->label('شحنة المورد')
                      ->relationship('productImport', 'supplier_name')
                      ->searchable()
                      ->preload()
                      ->required()
                      ->helperText('اختر شحنة الاستيراد المرتبطة بهذا الخيار'),

                  ]),
              ]),


            // edit image
            Forms\Components\Repeater::make('images')
              ->relationship('images')
              ->key('variant_images_list')
              ->label('صور الخيار')
              ->schema([
                Forms\Components\FileUpload::make('image')
                  ->label('الصورة')
                  ->image()
                  // ->multiple()
                  ->maxFiles(1)
                  // ->directory(fn($record) => "product_variants/{$record->product_variant_id}")
                  ->live()
                  ->directory(function ($get) {
                    $variantId = $get('../../id');
                    return "product_variants/{$variantId}";
                  })

                  ->visibility('public')
                  ->required()
                  ->getUploadedFileNameForStorageUsing(function ($file) {
                    return (string) Str::uuid() . '.webp';
                  })
                  ->imageEditor()
                  ->formatStateUsing(function ($state, $record) {
                    if (blank($state))
                      return [];
                    $path = is_array($state) ? $state : [$state];
                    return collect($path)->map(function ($p) use ($record) {
                      $dir = "product_variants/{$record->product_variant_id}/";
                      return str_contains($p, $dir) ? $p : $dir . $p;
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
                $existingImages = $record->images;
                $newItems = collect($state);

                foreach ($existingImages as $existingImage) {
                  $stillExists = $newItems->contains(fn($item) => ($item['id'] ?? null) == $existingImage->id);

                  if (!$stillExists) {
                    $filePath = "product_variants/{$record->id}/{$existingImage->image}";
                    if (Storage::disk('public')->exists($filePath)) {
                      Storage::disk('public')->delete($filePath);
                    }
                    $existingImage->delete();
                  }
                }

                foreach ($state as $item) {
                  $imageValue = $item['image'] ?? null;
                  if (!$imageValue)
                    continue;

                  $cleanName = is_array($imageValue) ? basename(array_values($imageValue)[0]) : basename($imageValue);

                  if (isset($item['id'])) {
                    $record->images()->where('id', $item['id'])->update(['image' => $cleanName]);
                  } else {
                    $record->images()->create(['image' => $cleanName]);
                  }
                }
              })
              ->grid(3)
              ->columnSpanFull(),

            // edit packages
            Forms\Components\Placeholder::make('no_packages_message')
              ->label('باقات الكميات (Packages)')
              ->content('لا توجد باقات أسعار مضافة لهذا الخيار حالياً.')
              ->visible(
                fn($record, $context) =>
                in_array($context, ['edit', 'view']) && $record && $record->packages()->count() === 0
              ),


            Forms\Components\Repeater::make('packages')
              ->relationship('packages')
              ->label(fn($record) => $record && $record->packages()->count() > 0 ? 'باقات الكميات (Packages)' : '')
              ->visible(fn($context) => in_array($context, ['edit', 'view']))
              ->schema([
                Forms\Components\Grid::make(2)
                  ->schema([
                    Forms\Components\TextInput::make('quantity')
                      ->label('عدد القطع في الباقة')
                      ->numeric()
                      ->required()
                      ->minValue(1),
                    Forms\Components\TextInput::make('price')
                      ->label('سعر الباقة')
                      ->numeric()
                      ->required()
                      ->prefix('$'),
                  ]),
              ])
              ->grid(2)
              ->columnSpanFull()
              ->defaultItems(0)
              ->createItemButtonLabel('إضافة باقة سعر جديدة'),
          ]),



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
                          'packages' => [],
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
            Forms\Components\Grid::make(3)
              ->schema([
                Forms\Components\Select::make('color_id')->label('اللون')->options(\App\Models\Color::pluck('color', 'id'))->required(),
                Forms\Components\Select::make('size_id')->label('الحجم')->options(\App\Models\Size::pluck('size', 'id'))->required(),
                Forms\Components\Select::make('material_id')->label('المادة')->options(\App\Models\Material::all()->mapWithKeys(function ($item) {
                  $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                  return [$item->id => $name];
                }))->required(),
                Forms\Components\TextInput::make('sku')
                  ->label('SKU')
                  ->required()
                  ->unique(table: 'product_variants', column: 'sku'),
                Forms\Components\TextInput::make('stock_quantity')->label('الكمية الاجمالية')->numeric()->required(),
                Forms\Components\TextInput::make('price')->label('السعر الافتراضي')->numeric()->required(),
              ]),

            Forms\Components\Grid::make(3)
              ->schema([
                Forms\Components\Select::make('product_import_id')
                  ->label('المورد المستورد')
                  ->relationship('productImport', 'supplier_name')
                  ->preload()
                  ->required(),
                Forms\Components\TextInput::make('discount')->label('الخصم %')->numeric()->default(0),

              ]),

            Forms\Components\Repeater::make('packages')
              ->label('باقات الأسعار لهذا الخيار')
              ->schema([
                Forms\Components\Grid::make(2)
                  ->schema([
                    Forms\Components\TextInput::make('quantity')->label('الكمية')->numeric()->required(),
                    Forms\Components\TextInput::make('price')->label('السعر')->numeric()->required(),
                  ]),
              ])
              ->collapsible()
              ->collapsed()
              ->itemLabel(fn(array $state): ?string => ($state['quantity'] ?? null) ? "باقة: {$state['quantity']} قطع" : "إضافة باقة جديدة")
              ->default([])
              ->columnSpanFull(),

            Forms\Components\FileUpload::make('images')
              ->label('صور الخيار')
              ->multiple()
              ->image()
              ->directory('product_variants')
          ])
          ->columnSpanFull()
          ->collapsible(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->query(ProductVariant::query()->with([
        'product',
        'color',
        'size',
        'material',
        'images',
        'packages',
        'productImport'
      ]))
      ->columns([
        Tables\Columns\ImageColumn::make('images.image')
          ->label('الصورة')
          ->circular()
          ->stacked()
          ->getStateUsing(function ($record) {
            if (!$record->images) {
              return [];
            }

            return $record->images->map(function ($img) use ($record) {
              $imageName = $img->image;

              $newPath = "product_variants/{$record->id}/{$imageName}";

              if (str_contains($imageName, 'product_variants/')) {
                return $imageName;
              }

              return $newPath;
            })->toArray();
          })
          ->disk('public'),

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

        Tables\Columns\TextColumn::make('discount')
          ->label('الخصم')
          ->formatStateUsing(function ($state) {
            if (is_null($state) || $state === '') {
              return '0%';
            }

            $number = floatval($state);

            $formatted = rtrim(rtrim(number_format($number, 2), '0'), '.');

            if (intval($number) == $number) {
              $formatted = intval($number);
            }

            return $formatted . '%';
          })
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true)

          ->searchable(),
        Tables\Columns\TextColumn::make('sku')
          ->label('الباركود البصري')
          ->formatStateUsing(fn($state) => $state ? new \Illuminate\Support\HtmlString(
            \DNS1D::getBarcodeHTML($state, 'C128', 1.5, 33)
          ) : '-')
          ->html()
          ->alignCenter()
          ->description(fn($record) => $record->sku),

        Tables\Columns\TextColumn::make('packages_count')
          ->label('باقات الأسعار')
          ->getStateUsing(function ($record) {
            $count = $record->packages()->count();

            return $count > 0 ? "{$count} باقات" : '-';
          })
          ->badge()
          ->color(fn($state): string => $state !== '-' ? 'success' : 'gray')
          ->alignCenter(),

        Tables\Columns\TextColumn::make('productImport.supplier_name')
          ->label('المورد')
          ->description(fn($record) => $record->productImport?->address)
          ->badge()
          ->color('info')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable(),
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

        Tables\Actions\Action::make('archive')
          ->label('أرشفة')
          ->icon('heroicon-o-archive-box')
          ->color('warning')
          ->requiresConfirmation()
          ->modalHeading('أرشفة الصنف')
          ->modalDescription('هل أنت متأكد أنك تريد أرشفة هذا الصنف؟ لن يظهر في القوائم النشطة.')
          ->modalSubmitActionLabel('نعم، قم بالأرشفة')
          ->action(function (ProductVariant $record) {
            $record->update(['deleted_at' => true]);

            \Filament\Notifications\Notification::make()
              ->title('تمت الأرشفة بنجاح')
              ->success()
              ->send();
          }),

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
