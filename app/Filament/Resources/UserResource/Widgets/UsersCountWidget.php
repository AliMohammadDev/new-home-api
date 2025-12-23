<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersCountWidget extends BaseWidget
{

  protected int|string|array $columnSpan = 1;
  protected function getStats(): array
  {
    return [
      Stat::make('المستخدمون', User::count()),
    ];
  }
}