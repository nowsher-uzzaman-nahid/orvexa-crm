<?php

namespace App\Filament\Resources\DealStages\Pages;

use App\Filament\Resources\DealStages\DealStageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDealStages extends ListRecords
{
    protected static string $resource = DealStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
