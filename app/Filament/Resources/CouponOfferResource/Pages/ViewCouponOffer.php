<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponOfferResource\Pages;

use App\Filament\Resources\CouponOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCouponOffer extends ViewRecord
{
    protected static string $resource = CouponOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
