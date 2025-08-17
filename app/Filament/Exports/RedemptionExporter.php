<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Redemption;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RedemptionExporter extends Exporter
{
    protected static ?string $model = Redemption::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('Redemption ID'),

            ExportColumn::make('code'),

            ExportColumn::make('created_at')
                ->label('Redemption Created At'),

            ExportColumn::make('used_at')
                ->label('Used At'),

            ExportColumn::make('user.id')
                ->label('User ID'),

            ExportColumn::make('user.first_name')
                ->label('User First Name'),

            ExportColumn::make('user.last_name')
                ->label('User Last Name'),

            ExportColumn::make('user.email')
                ->label('User Email'),

            ExportColumn::make('user.phone')
                ->label('User Phone'),

            ExportColumn::make('restaurant.id')
                ->label('Restaurant ID'),

            ExportColumn::make('restaurant.name')
                ->label('Restaurant Name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your redemption export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
