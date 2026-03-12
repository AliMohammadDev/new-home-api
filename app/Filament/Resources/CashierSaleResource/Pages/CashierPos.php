<?php

namespace App\Filament\Resources\CashierSaleResource\Pages;

use App\Filament\Resources\CashierSaleResource;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\Page;
use App\Models\SalesPointCashier;
use App\Models\CashierSale;
use App\Models\ProductVariant;

class CashierPos extends Page
{
  protected static string $resource = CashierSaleResource::class;
  protected static string $view = 'filament.resources.cashier-sale-resource.pages.cashier-pos';

  protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'شاشة الكاشير (POS)';
  protected static ?string $title = 'نقطة البيع';

  public string $barcode = '';
  public array $cart = [];
  public float $grandTotal = 0;

  public function scanBarcode()
  {
    if (empty($this->barcode)) {
      return;
    }

    $variant = ProductVariant::with('product')
      ->where('barcode', $this->barcode)
      ->orWhere('sku', $this->barcode)
      ->first();

    if ($variant) {
      $existingItemKey = collect($this->cart)->search(fn($item) => $item['variant_id'] === $variant->id);

      $netPrice = $variant->price - $variant->discount;

      if ($existingItemKey !== false) {
        if ($this->cart[$existingItemKey]['quantity'] < $variant->stock_quantity) {
          $this->cart[$existingItemKey]['quantity']++;
          $this->cart[$existingItemKey]['total'] = $this->cart[$existingItemKey]['quantity'] * $netPrice;
        } else {
          Notification::make()
            ->title('تنبيه')
            ->body('الكمية المطلوبة تتجاوز المخزون المتوفر!')
            ->warning()
            ->send();
        }
      } else {
        if ($variant->stock_quantity > 0) {
          $this->cart[] = [
            'variant_id' => $variant->id,
            'name' => $variant->product->name['ar'] ?? 'منتج غير مسمى',
            'sku' => $variant->sku,
            'barcode' => $variant->barcode ?? $variant->sku,
            'price' => $variant->price,
            'discount' => $variant->discount,
            'quantity' => 1,
            'total' => $netPrice,
          ];
        } else {
          Notification::make()
            ->title('نفذت الكمية')
            ->body('هذا المنتج غير متوفر في المستودع حالياً.')
            ->danger()
            ->send();
        }
      }

      $this->calculateTotal();

    } else {
      Notification::make()->title('خطأ')->body('المنتج غير موجود')->danger()->send();
    }

    $this->barcode = '';
  }

  public function removeItem($index)
  {
    unset($this->cart[$index]);
    $this->cart = array_values($this->cart);
    $this->calculateTotal();
  }





  public function calculateTotal()
  {
    $this->grandTotal = collect($this->cart)->sum('total');
  }

  public function checkout()
  {
    if (empty($this->cart)) {
      Notification::make()->title('السلة فارغة')->warning()->send();
      return;
    }

    DB::beginTransaction();

    try {
      $cashierId = SalesPointCashier::where('user_id', auth()->id())->value('id');
      $fatoraId = 1;

      foreach ($this->cart as $item) {
        CashierSale::create([
          'fatora_id' => $fatoraId,
          'sales_point_cashier_id' => $cashierId,
          'product_variant_id' => $item['variant_id'],
          'quantity' => $item['quantity'],
          'price' => $item['price'],
          'full_price' => $item['total'],
        ]);


      }

      DB::commit();

      Notification::make()->title('تم حفظ الفاتورة بنجاح')->success()->send();

      $this->dispatch('print-invoice', ['fatora_id' => $fatoraId]);

      $this->cart = [];
      $this->grandTotal = 0;

    } catch (\Exception $e) {
      DB::rollBack();
      Notification::make()->title('حدث خطأ أثناء حفظ الفاتورة')->body($e->getMessage())->danger()->send();
    }
  }
}