<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Redemption;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CouponsRedemptionChart extends ChartWidget
{
    protected static ?int $sort = 2;
    // protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Coupon Redemptions per Month';

    protected function getData(): array
    {
        $currentYear = now()->year;

        $redemptions = Redemption::selectRaw('EXTRACT(MONTH FROM created_at)::int AS month, COUNT(*) as count')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)')
            ->orderByRaw('EXTRACT(MONTH FROM created_at)')
            ->pluck('count', 'month');

        $data = collect(range(1, 12))->map(fn ($month) => $redemptions->get($month, 0))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Redemptions',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
            'labels' => collect(range(1, 12))->map(fn ($m) => Carbon::create(month: $m)?->format('M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Can also use 'bar' or 'area'
    }
}
