<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertiserResource\Pages;
use App\Models\Advertiser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertiserResource extends Resource
{
    protected static ?string $model = Advertiser::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Campaign Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Advertiser Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Business Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->default('active'),
                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('total_spent')
                    ->money('USD')
                    ->sortable()
                    ->getStateUsing(fn (Advertiser $record): float => $record->total_spent),
                Tables\Columns\TextColumn::make('budget_utilization')
                    ->label('Budget Utilization')
                    ->suffix('%')
                    ->getStateUsing(fn (Advertiser $record): float => round($record->budget_utilization, 1))
                    ->color(fn (Advertiser $record): string => $record->budget_utilization > 90 ? 'danger' : ($record->budget_utilization > 70 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvertisers::route('/'),
            'create' => Pages\CreateAdvertiser::route('/create'),
            'view' => Pages\ViewAdvertiser::route('/{record}'),
            'edit' => Pages\EditAdvertiser::route('/{record}/edit'),
        ];
    }
}