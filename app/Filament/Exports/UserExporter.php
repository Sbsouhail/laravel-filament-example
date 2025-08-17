<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\Gender;
use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('first_name'),
            ExportColumn::make('last_name'),
            ExportColumn::make('redemptions_count'),
            ExportColumn::make('gender')->getStateUsing(function (User $record) {
                /** @var Gender */
                $gender = $record->gender;

                return $gender->getLabel();
            }),
            ExportColumn::make('redemptions_summary')
                ->label('Redemptions Summary')
                ->getStateUsing(function (User $record) {
                    return $record->redemptions->map(function ($r) {
                        $restaurantName = $r->restaurant->name ?? 'Unknown';
                        $restaurantId = $r->restaurant->id ?? 'N/A';
                        $createdAt = $r->created_at?->format('Y-m-d') ?? 'N/A';
                        $usedAt = $r->used_at ? $r->used_at->format('Y-m-d') : 'Not Used';

                        return "{$r->code} | {$restaurantName} (ID: {$restaurantId}) | Created: {$createdAt} | Used: {$usedAt}";
                    })->implode('; ');
                }),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('is_admin'),
            ExportColumn::make('invite_limit'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
