<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
class NewOrderNotification extends Notification implements ShouldBroadcast
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(public $order)
  {
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
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
      ->getDatabaseMessage();
  }

  public function toBroadcast(object $notifiable): BroadcastMessage
  {
    return FilamentNotification::make()
      ->title('طلب جديد 📦')
      ->body('تم إنشاء طلب جديد برقم: ' . $this->order->id)
      ->icon('heroicon-o-shopping-cart')
      ->color('success')
      ->getBroadcastMessage();
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return [
      'title' => 'طلب جديد 📦',
      'body' => 'تم إنشاء طلب جديد',
    ];
  }
}
