<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('readonly')
                    ->content('Orders are created by Stripe checkout and cannot be edited manually.'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order details')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('customer_name')->default('Guest checkout'),
                        TextEntry::make('customer_email')->default('Pending'),
                        TextEntry::make('shipping_country')->default('Not collected'),
                        TextEntry::make('shipping_rate_label')->default('Not selected'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('total')
                            ->money('AUD', divideBy: 100),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('quantity'),
                                TextEntry::make('price')->money('AUD', divideBy: 100),
                                TextEntry::make('itemable_type')
                                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->default('Guest checkout')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->default('Pending')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->money('AUD', divideBy: 100)
                    ->sortable(),
                TextColumn::make('shipping_country')
                    ->default('—')
                    ->searchable(),
                TextColumn::make('shipping_rate_label')
                    ->default('—')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
