<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CouponOfferResource\Pages;
use App\Models\CouponOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponOfferResource extends Resource
{
    protected static ?string $model = CouponOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Businesses';

    protected static ?string $modelLabel = 'Offer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Offer Customization')
                    ->description('Customize the content displayed on the offer/ticket.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Offer Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Buy 1 Get 1 Free'),

                                Forms\Components\TextInput::make('item_description')
                                    ->label('Item Description')
                                    ->maxLength(255)
                                    ->placeholder('Main course'),

                                Forms\Components\TextInput::make('validity_period')
                                    ->label('Validity Period')
                                    ->maxLength(255)
                                    ->placeholder('Valid during lunch on work days'),

                                Forms\Components\TextInput::make('terms')
                                    ->label('Terms')
                                    ->maxLength(255)
                                    ->placeholder('Cannot be combined with other offers'),

                                Forms\Components\TextInput::make('button_title')
                                    ->label('Button Title')
                                    ->maxLength(255)
                                    ->placeholder('Redeem Now'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('validity_period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('terms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('button_title')
                    ->label('Button Title')
                    ->searchable(),
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
            'index' => Pages\ListCouponOffers::route('/'),
            'create' => Pages\CreateCouponOffer::route('/create'),
            'view' => Pages\ViewCouponOffer::route('/{record}'),
            'edit' => Pages\EditCouponOffer::route('/{record}/edit'),
        ];
    }
}
