<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineItemResource\Pages;
use App\Models\LineItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LineItemResource extends Resource
{
    protected static ?string $model = LineItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Campaign Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Line Item Details')
                    ->schema([
                        Forms\Components\Select::make('campaign_id')
                            ->relationship('campaign', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Performance Goals')
                    ->schema([
                        Forms\Components\TextInput::make('impressions_goal')
                            ->numeric()
                            ->default(0)
                            ->label('Impressions Goal'),
                        Forms\Components\TextInput::make('clicks_goal')
                            ->numeric()
                            ->default(0)
                            ->label('Clicks Goal'),
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
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Ad Specifications')
                    ->schema([
                        Forms\Components\Select::make('ad_size')
                            ->options([
                                '728x90' => 'Leaderboard (728x90)',
                                '300x250' => 'Medium Rectangle (300x250)',
                                '160x600' => 'Wide Skyscraper (160x600)',
                                '320x50' => 'Mobile Banner (320x50)',
                                '300x600' => 'Half Page (300x600)',
                                '970x250' => 'Billboard (970x250)',
                                'custom' => 'Custom Size',
                            ])
                            ->required()
                            ->default('300x250'),
                        Forms\Components\TextInput::make('ad_zone')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., homepage_above_fold'),
                        Forms\Components\Select::make('pricing_model')
                            ->options([
                                'CPM' => 'CPM (Cost Per Mille)',
                                'CPC' => 'CPC (Cost Per Click)',
                                'CPA' => 'CPA (Cost Per Action)',
                                'Flat' => 'Flat Rate',
                            ])
                            ->required()
                            ->default('CPM'),
                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),
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
                Tables\Columns\TextColumn::make('campaign.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'completed' => 'info',
                        'draft' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ad_size')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ad_zone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('impressions_delivered')
                    ->label('Impressions')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('impressions_pacing')
                    ->label('Pacing')
                    ->suffix('%')
                    ->getStateUsing(fn (LineItem $record): float => round($record->impressions_pacing, 1))
                    ->color(fn (LineItem $record): string => $record->is_under_delivering ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('budget')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('spent')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('campaign')
                    ->relationship('campaign', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('ad_size')
                    ->options([
                        '728x90' => 'Leaderboard (728x90)',
                        '300x250' => 'Medium Rectangle (300x250)',
                        '160x600' => 'Wide Skyscraper (160x600)',
                        '320x50' => 'Mobile Banner (320x50)',
                        '300x600' => 'Half Page (300x600)',
                        '970x250' => 'Billboard (970x250)',
                        'custom' => 'Custom Size',
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
            'index' => Pages\ListLineItems::route('/'),
            'create' => Pages\CreateLineItem::route('/create'),
            'view' => Pages\ViewLineItem::route('/{record}'),
            'edit' => Pages\EditLineItem::route('/{record}/edit'),
        ];
    }
}