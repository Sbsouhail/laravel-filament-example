<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Coupons';

    protected static ?string $modelLabel = 'Banner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- Offer Details Section ---
                Forms\Components\Section::make('Banner Details')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->relationship('restaurant', 'name'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Title')
                                    // ->required()
                                    ->maxLength(255),
                                // Forms\Components\TextInput::make('description')
                                //     ->label('Description')
                                //     ->required()
                                //     ->maxLength(255),
                            ]),
                    ]),

                // --- Media Upload Section ---
                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                            ->label('Banner Image')
                            ->collection('offer-banner')
                            ->disk('s3')
                            // ->visibility('private')
                            ->openable()
                            ->moveFiles()
                            ->image()
                            ->fetchFileInformation(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable(),
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'view' => Pages\ViewOffer::route('/{record}'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
