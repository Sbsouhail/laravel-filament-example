<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->visible(function (User $record) {
                return ! $record->is_admin;
            })
                ->before(function (Actions\DeleteAction $action, User $record) {
                    /*Get the currently authenticated user
                     * @var User
                    */
                    $currentUser = Filament::auth()->user();

                    // Check if the record being deleted is the current user
                    if ($record->id === $currentUser?->id || $record->is_admin) {
                        Notification::make()
                            ->warning()
                            ->title('You cannot delete your own account or other admins.')
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }
}
