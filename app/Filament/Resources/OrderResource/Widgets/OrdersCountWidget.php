<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersCountWidget extends ChartWidget
{
    protected static ?string $heading = 'عدد الطلبات حسب اليوم';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $orders = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'labels' => $orders->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'datasets' => [
                [
                    'label' => 'عدد الطلبات',
                    'data' => $orders->pluck('total')->toArray(),
                    'backgroundColor' => '#025043',
                    'borderColor' => '#025043',
                    'fill' => false,
                ],
            ],
        ];
    }
}
