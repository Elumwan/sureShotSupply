<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CameraListingResource\Pages;
use App\Models\CameraListing;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;

class CameraListingResource extends Resource
{
    protected static ?string $model = CameraListing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('make')
                    ->required()
                    ->maxLength(255),
                TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                TextInput::make('year')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue((int) date('Y') + 1),
                TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Select::make('condition')
                    ->required()
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                    ]),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('includes')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->helperText('Enter amount in cents (e.g. 32000 for $320.00)'),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->disk('public')
                    ->visibility('public')
                    ->multiple()
                    ->reorderable(true)
                    ->deletable(true)
                    ->previewable(true)
                    ->imageEditor(false)
                    ->imageResizeTargetWidth(null)
                    ->imageResizeTargetHeight(null)
                    ->imagePreviewHeight('100')
                    ->loadingIndicatorPosition('left')
                    ->removeUploadedFileButtonPosition('right')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(10240)
                    ->label('Camera images')
                    ->helperText('First image is shown as primary. Upload multiple angles.')
                    ->columnSpanFull(),
                Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'available' => 'Available',
                        'sold' => 'Sold',
                    ]),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('make')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('condition')
                    ->badge(),
                TextColumn::make('price')
                    ->money('USD', divideBy: 100)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCameraListings::route('/'),
            'create' => Pages\CreateCameraListing::route('/create'),
            'edit' => Pages\EditCameraListing::route('/{record}/edit'),
        ];
    }
}
