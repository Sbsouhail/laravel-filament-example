<?php

declare(strict_types=1);

namespace App\Filament\Resources\RestaurantResource\Pages;

use App\Filament\Resources\RestaurantResource;
use App\Filament\Resources\RestaurantResource\RelationManagers\RedemptionsRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurant extends ViewRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            RedemptionsRelationManager::class,
        ];
    }
}
