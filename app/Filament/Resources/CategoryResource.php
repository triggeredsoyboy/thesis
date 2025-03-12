<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\CategoryStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Blog & Article';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
                            ->columns(['sm' => 2])
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options(CategoryStatus::class)
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn(Category $record): ?string => $record->created_at?->diffForHumans()),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->label('Updated')
                                            ->content(fn(Category $record): ?string => $record->updated_at?->diffForHumans()),
                                    ])
                                    ->hidden(fn(?Category $record) => $record === null),
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
                fn(Model $record): string => route('filament.admin.resources.categories.view', $record),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
                        ->url(fn(Category $record): string => route('filament.admin.resources.categories.edit', $record)),
                    Tables\Actions\Action::make('delete')
                        ->color('danger')
                        ->action(fn(Category $record) => $record->delete())
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
                                Infolists\Components\TextEntry::make('status')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
