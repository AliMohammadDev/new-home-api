<?php

namespace App\Filament\Pages;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\SalesPointCashier;
use App\Models\CashierSale;
use Filament\Pages\Page;
class CashierPos extends Page
{

  protected static string $view = 'filament.resources.cashier-sale-resource.pages.cashier-pos';
  protected static ?string $slug = 'cashier-sales/pos';
  protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
  protected static ?string $navigationGroup = 'إدارة المبيعات';
  protected static ?string $navigationLabel = 'شاشة الكاشير (POS)';
  protected static ?int $navigationSort = 1;

  protected static ?string $title = 'نقطة البيع السريع';

  public static function shouldRegisterNavigation(array $parameters = []): bool
  {
    return true;
  }


  public static function isAuthorized(): bool
  {
    return auth()->user()->hasAnyRole(['super_admin', 'sales_point_cashier']);
  }

  public string $barcode = '';
  public array $cart = [];
  public float $grandTotal = 0;

  public function scanBarcode()
  {
    if (empty($this->barcode))
      return;

    $cashier = SalesPointCashier::with('salesPoint.warehouse')
      ->where('user_id', auth()->id())
      ->first();

    if (!$cashier || !$cashier->salesPoint?->warehouse) {
      Notification::make()->title('خطأ')->body('لا يوجد مستودع مرتبط بنقطة البيع هذه!')->danger()->send();
      return;
    }

    $warehouse = $cashier->salesPoint->warehouse;

    $variant = $warehouse->productVariants()
      ->where(function ($query) {
        $query->where('barcode', $this->barcode)
          ->orWhere('sku', $this->barcode);
      })
      ->first();

    if ($variant) {
      $stockInWarehouse = $variant->pivot->amount;
      $existingItemKey = collect($this->cart)->search(fn($item) => $item['variant_id'] === $variant->id);
      $netPrice = $variant->price - ($variant->discount ?? 0);

      if ($existingItemKey !== false) {
        if ($this->cart[$existingItemKey]['quantity'] < $stockInWarehouse) {
          $this->cart[$existingItemKey]['quantity']++;
          $this->cart[$existingItemKey]['total'] = $this->cart[$existingItemKey]['quantity'] * $netPrice;
        } else {
          Notification::make()->title('تنبيه')->body("الكمية غير كافية! المتاح: {$stockInWarehouse}")->warning()->send();
        }
      } else {
        if ($stockInWarehouse > 0) {
          $this->cart[] = [
            'variant_id' => $variant->id,
            'name' => $variant->product->name['ar'] ?? 'منتج غير مسمى',
            'sku' => $variant->sku,
            'price' => $variant->price,
            'discount' => $variant->discount ?? 0,
            'quantity' => 1,
            'total' => $netPrice,
          ];
        } else {
          Notification::make()->title('نفذت الكمية')->body('هذا المنتج غير متوفر في مستودعك حالياً.')->danger()->send();
        }
      }
      $this->calculateTotal();
    } else {
      Notification::make()->title('خطأ')->body('المنتج غير موجود في مستودعك')->danger()->send();
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

      foreach ($this->cart as $item) {

        CashierSale::create([
          'sales_point_cashier_id' => $cashierId,
          'product_variant_id' => $item['variant_id'],
          'quantity' => $item['quantity'],
          'price' => $item['price'],
          'discount' => $item['discount'],
          'full_price' => $item['total'],
        ]);
      }

      DB::commit();
      Notification::make()->title('تمت عملية البيع بنجاح')->success()->send();
      $this->cart = [];
      $this->grandTotal = 0;

    } catch (\Exception $e) {
      DB::rollBack();
      Notification::make()->title('حدث خطأ')->body($e->getMessage())->danger()->send();
    }
  }



}