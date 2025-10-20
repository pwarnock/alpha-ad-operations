<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Campaign Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\Select::make('advertiser_id')
                            ->relationship('advertiser', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Budget & Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required(),
                        Forms\Components\TextInput::make('spent')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0)
                            ->disabled(),
                        Forms\Components\Select::make('pricing_model')
                            ->options([
                                'CPM' => 'CPM (Cost Per Mille)',
                                'CPC' => 'CPC (Cost Per Click)',
                                'CPA' => 'CPA (Cost Per Action)',
                                'Flat' => 'Flat Rate',
                            ])
                            ->required()
                            ->default('CPM')
                            ->reactive(),
                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required()
                            ->helperText(fn (callable $get) => match ($get('pricing_model')) {
                                'CPM' => 'Cost per 1,000 impressions',
                                'CPC' => 'Cost per click',
                                'CPA' => 'Cost per action/conversion',
                                'Flat' => 'Total flat rate',
                                default => 'Rate based on pricing model',
                            }),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),
                        Forms\Components\TextInput::make('target_url')
                            ->url()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('targeting')
                            ->label('Targeting Options')
                            ->addActionLabel('Add targeting rule')
                            ->keyLabel('Parameter')
                            ->valueLabel('Value'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('advertiser.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        'draft' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('budget')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('spent')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget_utilization')
                    ->label('Utilization')
                    ->suffix('%')
                    ->getStateUsing(fn (Campaign $record): float => round($record->budget_utilization, 1))
                    ->color(fn (Campaign $record): string => $record->budget_utilization > 90 ? 'danger' : ($record->budget_utilization > 70 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('advertiser')
                    ->relationship('advertiser', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'view' => Pages\ViewCampaign::route('/{record}'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}