<?php

namespace App\Filament\Resources;

use App\Enums\ProneZone;
use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use App\Models\ProneArea;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ProneAreaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProneAreaResource\RelationManagers;

class ProneAreaResource extends Resource
{
    protected static ?string $model = ProneArea::class;

    protected static ?string $navigationGroup = 'Disaster Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

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
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->compact()
                            ->columns(['sm' => 2])
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('zone')
                                    ->options(ProneZone::class)
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn(ProneArea $record): ?string => $record->created_at?->diffForHumans()),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->label('Updated')
                                            ->content(fn(ProneArea $record): ?string => $record->updated_at?->diffForHumans()),
                                    ])
                                    ->hidden(fn(?ProneArea $record) => $record === null),
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
                fn(Model $record): string => route('filament.admin.resources.prone-areas.view', $record),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zone'),
                Tables\Columns\TextColumn::make('subdistricts_count')
                    ->label('Subdistricts')
                    ->counts('subdistricts'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('edit')
                        ->url(fn(ProneArea $record): string => route('filament.admin.resources.prone-areas.edit', $record)),
                    Tables\Actions\Action::make('delete')
                        ->color('danger')
                        ->action(fn(ProneArea $record) => $record->delete())
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
                    ])
                    ->columnSpan(['lg' => 2]),
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('zone')
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
            'index' => Pages\ListProneAreas::route('/'),
            'create' => Pages\CreateProneArea::route('/create'),
            'view' => Pages\ViewProneArea::route('/{record}'),
            'edit' => Pages\EditProneArea::route('/{record}/edit'),
        ];
    }
}
