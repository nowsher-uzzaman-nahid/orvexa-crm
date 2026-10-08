<?php

namespace App\Filament\Resources\DealStages\Pages;

use App\Filament\Resources\DealStages\DealStageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDealStage extends EditRecord
{
    protected static string $resource = DealStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
