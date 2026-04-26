<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductVariantExporter;
use App\Filament\Resources\ProductVariantResource\Pages;
use App\Filament\Resources\ProductVariantResource\RelationManagers\WarehousesRelationManager;
use App\Models\Material;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Resource;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;

class ProductVariantResource extends Resource
{
  protected static ?string $model = ProductVariant::class;
  protected static ?int $navigationSort = 5;
  protected static ?string $navigationIcon = 'heroicon-o-tag';
  protected static ?string $navigationLabel = 'خيارات المنتج';
  protected static ?string $navigationGroup = 'إدارة المنتجات';
  protected static ?string $modelLabel = 'خيار المنتج';
  protected static ?string $pluralModelLabel = 'خيارات المنتج';


  public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
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
        Section::make('تعديل بيانات الخيار')
          ->visible(fn($context) => in_array($context, ['edit', 'view']))
          ->schema([
            Grid::make(3)
              ->schema([
                Select::make('color_id')
                  ->label('اللون')
                  ->searchable()
                  ->preload()
                  ->options(Color::all()->mapWithKeys(function ($item) {
                    $name = $item->color[app()->getLocale()] ?? $item->color['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required(),
                Select::make('size_id')
                  ->label('الحجم')
                  ->options(Size::pluck('size', 'id'))
                  ->searchable()
                  ->preload()
                  ->required(),
                Select::make('material_id')
                  ->label('المادة')
                  ->searchable()
                  ->preload()
                  ->options(Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required(),


                TextInput::make('sku')
                  ->label('رمز الـ SKU')
                  ->placeholder('مثال: SHIRT-RED-L')
                  ->unique(ignoreRecord: true)
                  ->required()
                  ->live()
                  ->helperText('هذا الرمز الإداري الخاص بالخيار')
                  ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                  ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                TextInput::make('barcode')
                  ->label('الباركود (Barcode)')
                  ->placeholder('أدخل رقم الباركود')
                  ->prefixIcon('heroicon-m-qr-code')
                  ->unique(ignoreRecord: true)
                  ->helperText('يمكنك إدخال رقم الباركود الدولي (EAN/UPC) هنا')
                  ->live(),

                ViewField::make('barcode_visual')
                  ->label('الباركود الحالي (للمسح)')
                  ->view('filament.forms.components.barcode-display')
                  ->columnSpan(1)
                  ->visible(fn($record) => filled($record?->barcode)),

                Grid::make(3)
                  ->schema([
                    Forms\Components\TextInput::make('price')->label('السعر'),
                    Forms\Components\TextInput::make('discount')->label('الخصم %'),

                    Forms\Components\TextInput::make('stock_quantity')
                      ->label('الكمية الحالية')
                      ->numeric()
                      ->required(),

                  ]),
              ]),


            // edit image
            Repeater::make('images')
              ->relationship('images')
              ->key('variant_images_list')
              ->label('صور الخيار')
              ->schema([
                FileUpload::make('image')
                  ->label('الصورة')
                  ->image()
                  ->maxFiles(1)
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
            Placeholder::make('no_packages_message')
              ->label('باقات الكميات (Packages)')
              ->content('لا توجد باقات أسعار مضافة لهذا الخيار حالياً.')
              ->visible(
                fn($record, $context) =>
                in_array($context, ['edit', 'view']) && $record && $record->packages()->count() === 0
              ),


            Repeater::make('packages')
              ->relationship('packages')
              ->label(fn($record) => $record && $record->packages()->count() > 0 ? 'باقات الكميات (Packages)' : '')
              ->visible(fn($context) => in_array($context, ['edit', 'view']))
              ->schema([
                Grid::make(2)
                  ->schema([
                    TextInput::make('quantity')
                      ->label('عدد القطع في الباقة')
                      ->numeric()
                      ->required()
                      ->minValue(1),
                    TextInput::make('price')
                      ->label('سعر الباقة')
                      ->numeric()
                      ->required()
                      ->prefix('$'),
                  ]),
              ])
              ->grid(1)
              ->columnSpanFull()
              ->defaultItems(0)
              ->addActionLabel('إضافة باقة سعر جديدة')
          ]),

        Section::make('أدوات التوليد السريع')
          ->visible(fn($context) => $context === 'create')
          ->schema([
            Grid::make(3)
              ->schema([
                Select::make('temp_colors')
                  ->label('الألوان')
                  ->multiple()
                  ->searchable()
                  ->preload()
                  ->live()
                  ->options(Color::all()->mapWithKeys(function ($item) {
                    $name = $item->color[app()->getLocale()] ?? $item->color['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->dehydrated(false),

                Select::make('temp_sizes')
                  ->label('الأحجام')
                  ->multiple()
                  ->options(Size::pluck('size', 'id'))
                  ->searchable()
                  ->preload()
                  ->live()
                  ->dehydrated(false)
                  ->createOptionUsing(function (array $data) {
                    return Size::create(['size' => $data['size']])->id;
                  })
                  ->createOptionForm([
                    TextInput::make('size')
                      ->label('حجم جديد')
                      ->required(),
                  ])
                  ->afterStateUpdated(function ($old, $state) {
                    $removedIds = array_diff($old ?? [], $state ?? []);

                    foreach ($removedIds as $id) {
                      $size = Size::find($id);
                      if ($size && $size->productVariants()->count() === 0) {
                        $size->delete();
                      }
                    }
                  }),

                Select::make('temp_materials')
                  ->label('المواد')
                  ->multiple()
                  ->searchable()
                  ->preload()
                  ->live()
                  ->options(Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->dehydrated(false)
                  ->createOptionForm([
                    Forms\Components\TextInput::make('material_ar')
                      ->label('اسم المادة (بالعربية)')
                      ->required(),
                    Forms\Components\TextInput::make('material_en')
                      ->label('اسم المادة (بالإنجليزية)')
                      ->required(),
                  ])
                  ->createOptionUsing(function (array $data) {
                    $newMaterial = Material::create([
                      'material' => [
                        'ar' => $data['material_ar'],
                        'en' => $data['material_en'],
                      ],
                    ]);
                    return $newMaterial->id;
                  })
                  ->afterStateUpdated(function ($old, $state) {
                    $removedIds = array_diff($old ?? [], $state ?? []);
                    foreach ($removedIds as $id) {
                      $material = Material::find($id);
                      if ($material && $material->productVariants()->count() === 0) {
                        $material->delete();
                      }
                    }
                  }),

              ]),
            Actions::make([
              Forms\Components\Actions\Action::make('generate_variants')
                ->label('توليد الخيارات تلقائياً')
                ->icon('heroicon-o-sparkles')
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
                          'stock_quantity' => 0,
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

        Select::make('product_id')
          ->label('المنتج')
          ->relationship('product', 'id')
          ->searchable()
          ->preload()
          ->getOptionLabelFromRecordUsing(fn($record) => $record->name[app()->getLocale()] ?? $record->name['en'] ?? '')
          ->getSearchResultsUsing(function (string $search) {
            return Product::where('name->' . app()->getLocale(), 'like', "%{$search}%")
              ->orWhere('name->en', 'like', "%{$search}%")
              ->limit(50)
              ->pluck('name', 'id')
              ->map(fn($name) => $name[app()->getLocale()] ?? $name['en'] ?? '');
          })
          ->required()
          ->visible(fn($context) => $context === 'create')
          ->columnSpanFull(),

        Repeater::make('variants')
          ->label('قائمة الخيارات الناتجة')
          ->visible(fn($context) => $context === 'create')
          ->schema([
            Grid::make(2)
              ->schema([
                Select::make('color_id')
                  ->label('اللون')
                  ->searchable()
                  ->preload()
                  ->options(Color::all()->mapWithKeys(function ($item) {
                    $name = $item->color[app()->getLocale()] ?? $item->color['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required(),
                Select::make('size_id')
                  ->label('الحجم')
                  ->searchable()
                  ->preload()
                  ->options(Size::pluck('size', 'id'))
                  ->required(),
                Select::make('material_id')->label('المادة')
                  ->searchable()
                  ->preload()
                  ->options(Material::all()->mapWithKeys(function ($item) {
                    $name = $item->material[app()->getLocale()] ?? $item->material['en'] ?? 'N/A';
                    return [$item->id => $name];
                  }))
                  ->required(),


                TextInput::make('sku')
                  ->label('SKU')
                  ->required()
                  ->unique(table: 'product_variants', column: 'sku', ignoreRecord: true)
                  ->rules([
                    fn($get) => function (string $attribute, $value, $fail) use ($get) {
                      $allSkus = collect($get('../../variants'))
                        ->pluck('sku')
                        ->filter()
                        ->toArray();
                      $counts = array_count_values($allSkus);
                      if (isset($counts[$value]) && $counts[$value] > 1) {
                        $fail('عذراً، رمز SKU هذا مكرر في القائمة .');
                      }
                    },
                  ])
                  ->live(onBlur: true)
                  ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                  ->dehydrateStateUsing(fn($state) => strtoupper($state)),


                // TextInput::make('sku')
                //   ->label('SKU')
                //   ->required()
                //   ->unique(table: 'product_variants', column: 'sku')
                //   ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                //   ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                TextInput::make('barcode')
                  ->label('الباركود')
                  ->unique(table: 'product_variants', column: 'barcode')
                  ->prefixIcon('heroicon-m-qr-code')
                  ->placeholder('barcode'),


                TextInput::make('price')
                  ->label('السعر الافتراضي')
                  ->numeric()
                  ->required(),
              ]),

            Repeater::make('packages')
              ->label('باقات الأسعار لهذا الخيار')
              ->schema([
                Grid::make(2)
                  ->schema([
                    TextInput::make('quantity')
                      ->label('الكمية')
                      ->numeric()
                      ->required(),
                    TextInput::make('price')
                      ->label('السعر')
                      ->numeric()
                      ->required(),
                  ]),
              ])
              ->collapsible()
              ->collapsed()
              ->itemLabel(fn(array $state): ?string => ($state['quantity'] ?? null) ? "باقة: {$state['quantity']} قطع" : "إضافة باقة جديدة")
              ->default([])
              ->columnSpanFull(),

            FileUpload::make('images')
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
      ]))
      ->columns([
        ImageColumn::make('images.image')
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
        TextColumn::make('product.name')
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
        ColorColumn::make('color.hex_code')
          ->label('اللون')
          ->sortable(),
        TextColumn::make('size.size')->label('الحجم')->sortable()->searchable(),
        TextColumn::make('material.material')->label('المادة')->sortable()->searchable(),
        TextColumn::make('stock_quantity')->label('الكمية')->sortable()->searchable(),
        TextColumn::make('price')
          ->label('السعر')
          ->sortable()
          ->searchable(),

        TextColumn::make('discount')
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

        TextColumn::make('final_price')
          ->label('السعر النهائي')
          ->getStateUsing(fn($record) => $record->final_price)
          ->money('USD', locale: 'en_US')
          ->weight('bold')
          ->color('success')
          ->description('السعر بعد تطبيق الخصم')
          ->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('visual_barcode')
          ->label('الباركود والترميز')
          ->getStateUsing(fn($record) => $record->barcode)
          ->formatStateUsing(fn($state) => $state ? new \Illuminate\Support\HtmlString(
            \DNS1D::getBarcodeHTML((string) $state, 'C128', 1.5, 33)
          ) : '-')
          ->html()
          ->alignCenter()
          ->description(fn($record) => "{$record->barcode}"),

        TextColumn::make('sku')
          ->label('رمز SKU')
          ->searchable()
          ->sortable()
          ->copyable()
          ->copyMessage('تم نسخ الرمز')
          ->weight('bold'),

        TextColumn::make('packages_count')
          ->label('باقات الأسعار')
          ->counts('packages')
          ->formatStateUsing(fn($state) => $state > 0 ? "{$state} باقات" : '-')
          ->badge()
          ->color(fn($state): string => $state !== '-' ? 'success' : 'gray')
          ->alignCenter(),


      ])
      ->defaultSort('created_at', 'DESC')
      ->filters([
        SelectFilter::make('product_id')
          ->label('المنتج')
          ->relationship('product', 'id')
          ->getOptionLabelFromRecordUsing(fn(Product $record) => $record->name[app()->getLocale()] ?? $record->name['en'] ?? '')
          ->searchable()
          ->preload(),


        SelectFilter::make('color_id')
          ->label('اللون')
          ->relationship('color', 'id')
          ->getOptionLabelFromRecordUsing(function ($record) {
            return $record->color[app()->getLocale()]
              ?? $record->color['en']
              ?? 'N/A';
          })
          ->searchable()
          ->preload(),

        SelectFilter::make('size')
          ->label('الحجم')
          ->relationship('size', 'size')
          ->searchable()
          ->preload(),


        SelectFilter::make('material_id')
          ->label('المادة')
          ->relationship('material', 'id')
          ->getOptionLabelFromRecordUsing(function ($record) {
            return $record->material[app()->getLocale()]
              ?? $record->material['en']
              ?? 'N/A';
          })
          ->searchable()
          ->preload(),

        TrashedFilter::make()
          ->label('حالة الأرشفة')
          ->native(false),
      ])
      ->actions([
        EditAction::make(),
        ViewAction::make()->label('عرض'),

        DeleteAction::make()
          ->label('أرشفة'),

        RestoreAction::make()
          ->label('استعادة'),

        ForceDeleteAction::make()
          ->label('حذف نهائي'),
      ])
      ->headerActions([
        ExportAction::make()
          ->exporter(ProductVariantExporter::class)
          ->color('success')->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          DeleteBulkAction::make()
            ->label('أرشفة المحدد'),

          RestoreBulkAction::make()
            ->label('استعادة المحدد'),

          ForceDeleteBulkAction::make()
            ->label('حذف نهائي للمحدد'),
        ]),
        ExportBulkAction::make()
          ->exporter(ProductVariantExporter::class)
          ->color('success')
          ->icon('heroicon-o-arrow-down-tray')
          ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
          ->visible(fn() => auth()->user()->hasRole('super_admin')),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      WarehousesRelationManager::class,
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