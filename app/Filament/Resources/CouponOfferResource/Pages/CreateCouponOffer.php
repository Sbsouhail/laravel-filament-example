<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponOfferResource\Pages;

use App\Filament\Resources\CouponOfferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCouponOffer extends CreateRecord
{
    protected static string $resource = CouponOfferResource::class;
}
