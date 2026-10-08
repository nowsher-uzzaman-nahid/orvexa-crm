<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->maxLength(255),

                        TextInput::make('job_title')
                            ->label('Job Title')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('CRM Information')
                    ->schema([
                        Select::make('lead_source_id')
                            ->label('Lead Source')
                            ->relationship('source', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('lead_status_id')
                            ->label('Lead Status')
                            ->relationship('status', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('estimated_value')
                            ->label('Estimated Value')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0),
                    ])
                    ->columns(2),

                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
