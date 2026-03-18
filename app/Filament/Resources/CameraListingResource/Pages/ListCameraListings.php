<?php

namespace App\Filament\Resources\CameraListingResource\Pages;

use App\Filament\Resources\CameraListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCameraListings extends ListRecords
{
    protected static string $resource = CameraListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
