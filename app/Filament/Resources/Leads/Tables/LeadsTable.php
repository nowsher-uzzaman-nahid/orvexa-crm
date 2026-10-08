<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use App\Services\LeadConversionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(
                        fn ($state, $record) =>
                            trim($record->first_name . ' ' . $record->last_name)
                    )
                    ->searchable(['first_name','last_name'])
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone'),

                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('source.name')
                    ->label('Source')
                    ->sortable(),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('customer.customer_code')
                    ->label('Customer')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->sortable(),

                TextColumn::make('estimated_value')
                    ->label('Estimated Value')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),

                // TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('lead_status_id')
                    ->label('Status')
                    ->relationship('status', 'name'),

                SelectFilter::make('lead_source_id')
                    ->label('Source')
                    ->relationship('source', 'name'),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedUser', 'name'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('convert')
                    ->label('Convert')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convert Lead')
                    ->modalDescription(
                        'This will convert the lead into a customer. This action cannot be undone.'
                    )
                    ->action(function ($record) {
                        app(LeadConversionService::class)->convert($record);

                        Notification::make()
                            ->success()
                            ->title('Lead converted')
                            ->body('The lead has been successfully converted to a customer.')
                            ->send();
                    })
                    ->visible(fn (Lead $record) => $record->converted_at === null)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
