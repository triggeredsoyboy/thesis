<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subdistrict;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubdistrictResource\Pages;
use App\Filament\Resources\SubdistrictResource\RelationManagers;
use App\Forms\Components\Maps;

class SubdistrictResource extends Resource
{
    protected static ?string $model = Subdistrict::class;

    protected static ?string $navigationGroup = 'Disaster Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->string()
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->autofocus()
                                    ->autocomplete(false)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->string()
                                    ->minLength(3)
                                    ->maxLength(100)
                                    ->autocomplete(false)
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Forms\Components\RichEditor::make('description')
                                    ->string()
                                    ->minLength(10)
                                    ->maxLength(10000)
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->compact()
                            ->columns(['sm' => 2]),
                        Forms\Components\Repeater::make('vulnerabilities')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'Age Group' => [
                                            'toddler' => 'Toddler',
                                            'children' => 'Children',
                                            'teenager' => 'Teenager',
                                            'elderly' => 'Elderly',
                                        ],
                                        'Health Condition' => [
                                            'physical_disability' => 'Physical Disability',
                                            'mental_disability' => 'Mental Disability',
                                        ],
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                            ])
                            ->itemLabel(fn(array $state): ?string => str($state['type'])->headline() ?? null)
                            ->addActionLabel('Add vulnerability')
                            ->columns(['sm' => 2]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('proneAreas')
                                    ->relationship(titleAttribute: 'name')
                                    ->multiple()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn(Subdistrict $record): ?string => $record->created_at?->diffForHumans()),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->label('Updated')
                                            ->content(fn(Subdistrict $record): ?string => $record->updated_at?->diffForHumans()),
                                    ])
                                    ->hidden(fn(?Subdistrict $record) => $record === null),
                            ])
                            ->compact(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(
                fn(Model $record): string => route('filament.admin.resources.subdistricts.view', $record),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proneAreas.zone')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('edit')
                        ->url(fn(Subdistrict $record): string => route('filament.admin.resources.subdistricts.edit', $record)),
                    Tables\Actions\Action::make('delete')
                        ->color('danger')
                        ->action(fn(Subdistrict $record) => $record->delete())
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->color('danger')
                    ->action(fn(Collection $records) => $records->each->delete())
                    ->requiresConfirmation(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('slug'),
                                Infolists\Components\TextEntry::make('description')
                                    ->placeholder('There\'s no description.')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ])
                            ->compact()
                            ->columns(['sm' => 2]),
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('vulnerabilities')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('type')
                                            ->formatStateUsing(fn(string $state): string => str($state)->headline()),
                                        Infolists\Components\TextEntry::make('amount'),
                                    ])
                                    ->columns(['sm' => 2])
                                    ->grid(['sm' => 2]),
                            ])
                            ->compact(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('proneAreas.zone')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->since(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->since(),
                            ])
                            ->compact(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
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
            'index' => Pages\ListSubdistricts::route('/'),
            'create' => Pages\CreateSubdistrict::route('/create'),
            'view' => Pages\ViewSubdistrict::route('/{record}'),
            'edit' => Pages\EditSubdistrict::route('/{record}/edit'),
        ];
    }
}
