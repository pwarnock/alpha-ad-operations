<?php

namespace App\Filament\Publisher\Resources;

use App\Filament\Publisher\Resources\SavedReportResource\Pages;
use App\Models\SavedReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavedReportResource extends Resource
{
    protected static ?string $model = SavedReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Public Report')
                            ->helperText('Public reports can be viewed by all users in your organization'),
                    ]),

                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\Select::make('filters.advertiser_id')
                            ->label('Advertisers')
                            ->options(\App\Models\Advertiser::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('filters.campaign_id')
                            ->label('Campaigns')
                            ->options(\App\Models\Campaign::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('filters.line_item_id')
                            ->label('Line Items')
                            ->options(\App\Models\LineItem::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Date Range')
                    ->schema([
                        Forms\Components\DatePicker::make('filters.date_from')
                            ->label('Date From'),

                        Forms\Components\DatePicker::make('filters.date_to')
                            ->label('Date To'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Metrics & Grouping')
                    ->schema([
                        Forms\Components\Select::make('metrics')
                            ->label('Metrics')
                            ->options([
                                'impressions' => 'Impressions',
                                'clicks' => 'Clicks',
                                'revenue' => 'Revenue',
                                'ctr' => 'CTR',
                                'ecpm' => 'eCPM',
                                'cpc' => 'CPC',
                            ])
                            ->multiple()
                            ->default(['impressions', 'clicks', 'revenue'])
                            ->required(),

                        Forms\Components\Select::make('group_by')
                            ->label('Group By')
                            ->options([
                                'date' => 'Date',
                                'advertiser' => 'Advertiser',
                                'campaign' => 'Campaign',
                                'line_item' => 'Line Item',
                            ])
                            ->multiple()
                            ->default(['date']),
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

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Visibility')
                    ->boolean()
                    ->trueLabel('Public reports only')
                    ->falseLabel('Private reports only')
                    ->native(false),
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
            'index' => Pages\ListSavedReports::route('/'),
            'create' => Pages\CreateSavedReport::route('/create'),
            'view' => Pages\ViewSavedReport::route('/{record}'),
            'edit' => Pages\EditSavedReport::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Apply tenant filtering if tenant is available
        if (auth()->check() && auth()->user()->tenant_id) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        return $query;
    }
}