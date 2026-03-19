<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageImages')
                ->label('Manage images')
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->visible(fn (): bool => $this->record->getMedia('images')->isNotEmpty())
                ->modalHeading('Manage product images')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->registerModalActions($this->getManageImageActions())
                ->modalContent(fn (Action $action): View => view('filament.actions.manage-images', [
                    'action' => $action,
                    'mediaItems' => $this->record->getMedia('images'),
                ])),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getManageImageActions(): array
    {
        return $this->record
            ->getMedia('images')
            ->map(fn (Media $media): Action => Action::make("deleteMedia{$media->getKey()}")
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () use ($media): void {
                    $media->delete();

                    $this->record->refresh();
                    $this->fillForm();
                    $this->replaceMountedAction('manageImages');
                }))
            ->all();
    }
}
