<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponOfferResource\Pages;

use App\Filament\Resources\CouponOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouponOffer extends EditRecord
{
    protected static string $resource = CouponOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
