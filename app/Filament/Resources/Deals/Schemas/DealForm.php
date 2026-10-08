<?php

namespace App\Filament\Resources\Deals\Schemas;

use App\Enums\DealStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Deal Information')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship(
                                'customer',
                                'customer_code',
                                fn ($query) => $query->with('contact')
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) =>
                                    $record->customer_code
                                    . ' — '
                                    . $record->contact->first_name
                                    . ' '
                                    . $record->contact->last_name
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('value')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),

                        TextInput::make('currency')
                            ->default('USD')
                            ->required()
                            ->maxLength(3),

                        Select::make('deal_stage_id')
                            ->label('Stage')
                            ->relationship(
                                'stage', 
                                'name',
                                fn ($query) => $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedUser', 'name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->name} — {$record->email}"
                            )
                            ->searchable(['name', 'email'])
                            ->preload(),

                        DatePicker::make('expected_close_date')
                            ->label('Expected Close Date'),
                    ])
                    ->columns(2),

                Section::make('Deal Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                DealStatus::OPEN->value => 'Open',
                                DealStatus::WON->value => 'Won',
                                DealStatus::LOST->value => 'Lost',
                            ])
                            ->default(DealStatus::OPEN->value)
                            ->required()
                            ->live(),

                        DateTimePicker::make('closed_at')
                            ->label('Closed At')
                            ->visible(
                                fn (Get $get): bool =>
                                    in_array(
                                        $get('status'),
                                        [
                                            DealStatus::WON->value,
                                            DealStatus::LOST->value,
                                        ],
                                        true
                                    )
                            ),

                        Textarea::make('lost_reason')
                            ->label('Lost Reason')
                            ->rows(3)
                            ->visible(
                                fn (Get $get): bool =>
                                    $get('status') === DealStatus::LOST->value
                            ),

                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
