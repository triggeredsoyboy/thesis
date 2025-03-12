<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use App\Enums\PostStatus;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = 'Blog & Article';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
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
                                Forms\Components\RichEditor::make('excerpt')
                                    ->string()
                                    ->minLength(10)
                                    ->maxLength(10000)
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ])
                                    ->columnSpanFull()
                                    ->required(),
                            ])
                            ->compact()
                            ->columns(['sm' => 2]),
                        Forms\Components\Builder::make('body')
                            ->blocks([
                                Forms\Components\Builder\Block::make('paragraph')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content')
                                            ->string()
                                            ->minLength(10)
                                            ->maxLength(10000)
                                            ->disableToolbarButtons([
                                                'attachFiles',
                                            ])
                                            ->columnSpanFull()
                                            ->required(),
                                    ]),
                                Forms\Components\Builder\Block::make('media')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->image(),
                                    ])
                            ])
                            ->addActionLabel('Add blocks')
                            ->blockNumbers(false)
                            ->required(),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options(PostStatus::class)
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Select::make('category_id')
                                    ->relationship(name: 'category', titleAttribute: 'name')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn(Post $record): ?string => $record->created_at?->diffForHumans()),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->label('Updated')
                                            ->content(fn(Post $record): ?string => $record->updated_at?->diffForHumans()),
                                    ])
                                    ->hidden(fn(?Post $record) => $record === null),
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
                fn(Model $record): string => route('filament.admin.resources.posts.view', $record),
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
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
                        ->url(fn(Post $record): string => route('filament.admin.resources.posts.edit', $record)),
                    Tables\Actions\Action::make('delete')
                        ->color('danger')
                        ->action(fn(Post $record) => $record->delete())
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
