<?php

declare(strict_types=1);

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use App\Models\Redemption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('restaurant_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('restaurant_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.id')
                    ->numeric()
                    ->sortable()
                    ->label('User ID'),
                Tables\Columns\TextColumn::make('user.first_name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('user.email')
                    ->sortable()
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('used_at')
                    ->default('Not Used')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsUsed')
                    ->label('Mark as Used')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Redemption $record) => $record->used_at === null)
                    ->action(fn (Redemption $record) => $record->markAsUsed()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
