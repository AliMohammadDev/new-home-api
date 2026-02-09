<?php

namespace App\Notifications;

use App\Filament\Resources\OrderResource; // تأكد من عمل import للـ Resource
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;

class NewOrderNotification extends Notification implements ShouldBroadcast
{
  use Queueable;

  public function __construct(public $order)
  {
  }

  public function via(object $notifiable): array
  {
    return ['database', 'broadcast'];
  }

  public function toDatabase(object $notifiable): array
  {
    return FilamentNotification::make()
      ->title('طلب جديد 📦')
      ->body('تم إنشاء طلب جديد برقم: ' . $this->order->id)
      ->icon('heroicon-o-shopping-cart')
      ->color('success')
      ->actions([
        Action::make('view')
          ->label('عرض الطلب')
          ->url(OrderResource::getUrl('view', ['record' => $this->order->id]))
          ->button(),
      ])
      ->getDatabaseMessage();
  }

  public function toBroadcast(object $notifiable): BroadcastMessage
  {
    return FilamentNotification::make()
      ->title('طلب جديد 📦')
      ->body('تم إنشاء طلب جديد برقم: ' . $this->order->id)
      ->icon('heroicon-o-shopping-cart')
      ->color('success')
      ->actions([
        Action::make('view')
          ->label('عرض الطلب')
          ->url(OrderResource::getUrl('view', ['record' => $this->order->id])),
      ])
      ->getBroadcastMessage();
  }

  public function toArray(object $notifiable): array
  {
    return [
      'order_id' => $this->order->id,
    ];
  }
}