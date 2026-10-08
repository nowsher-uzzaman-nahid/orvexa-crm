<?php

namespace App\Filament\Resources\DealStages;

use App\Filament\Resources\DealStages\Pages\CreateDealStage;
use App\Filament\Resources\DealStages\Pages\EditDealStage;
use App\Filament\Resources\DealStages\Pages\ListDealStages;
use App\Filament\Resources\DealStages\Schemas\DealStageForm;
use App\Filament\Resources\DealStages\Tables\DealStagesTable;
use App\Models\DealStage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DealStageResource extends Resource
{
    protected static ?string $model = DealStage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DealStageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DealStagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDealStages::route('/'),
            'create' => CreateDealStage::route('/create'),
            'edit' => EditDealStage::route('/{record}/edit'),
        ];
    }
}
