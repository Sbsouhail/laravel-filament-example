<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Redemption;
use App\Models\Restaurant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCouponsPerUser = Restaurant::sum('coupon_per_user');
        $totalUsers = User::count();
        $totalCouponsIssued = $totalCouponsPerUser * $totalUsers;

        $totalRedeemed = Redemption::count();
        $monthlyRedeemed = Redemption::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        // $remainingCoupons = max($totalCouponsIssued - $monthlyRedeemed, 0);
        $redemptionRate = $totalCouponsIssued > 0
            ? round(($monthlyRedeemed / $totalCouponsIssued) * 100, 2)
            : 0;

        return [
            Stat::make('Total Coupons Issued this month', $totalCouponsIssued)
                ->description($monthlyRedeemed.' redeemed this month')
                ->descriptionIcon('heroicon-m-ticket'),

            Stat::make('Coupons Redeemed', $totalRedeemed)
                ->description('In total')
                ->descriptionIcon('heroicon-m-check-circle'),

            // Stat::make('Coupons Remaining', $remainingCoupons)
            //     ->description('Unredeemed coupons')
            //     ->descriptionIcon('heroicon-m-clock'),

            Stat::make('Redemption Rate', $redemptionRate . '%')
                ->description('For this month')
                ->descriptionIcon('heroicon-m-chart-bar'),

            Stat::make('Registered Users', $totalUsers)
                ->description('Eligible to redeem coupons')
                ->descriptionIcon('heroicon-m-users'),
        ];
    }
}
