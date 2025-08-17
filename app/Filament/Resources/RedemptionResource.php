<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Exports\RedemptionExporter;
use App\Filament\Resources\RedemptionResource\Pages;
use App\Models\Redemption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class RedemptionResource extends Resource
{
    protected static ?string $model = Redemption::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Coupons';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'first_name')
                    ->required(),
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('used_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->numeric()
                    ->sortable(),
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
                ExportAction::make()
                    ->exporter(RedemptionExporter::class)
                    ->modifyQueryUsing(
                        fn (Builder $query) => $query->with('user', 'restaurant')
                    ),
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

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedemptions::route('/'),
            // 'create' => Pages\CreateRedemption::route('/create'),
            // 'edit' => Pages\EditRedemption::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
