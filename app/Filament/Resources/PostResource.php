<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResourcesResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Create New Post')->tabs([
                    Tab::make('Post Details')
                        ->icon('heroicon-s-chat-bubble-left-right')
                        ->schema([
                            TextInput::make('title')
                                ->rules(['min:5', 'max:10'])
                                ->required(),
                            TextInput::make('slug')->unique(ignoreRecord: true)->required(),
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                //->options(
                                //(Category::all()->pluck('name', 'id')))
                                ->required(),
                            ColorPicker::make('color')->required(),
                        ]),
                    Tab::make('Content')
                        ->icon('heroicon-s-server-stack')
                        ->schema([
                            MarkdownEditor::make('content')->required()->columnSpan('full'),
                        ]),
                    Tab::make('Meta')
                        ->icon('heroicon-s-arrow-up-on-square')
                        ->schema([
                            FileUpload::make('thumbnail')->disk('public')->directory('thumbnail'),
                            TagsInput::make('tags')->required(),
                            Checkbox::make('published')
                        ])
                ])->columnSpanFull(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('slug')->sortable()->searchable()->toggleable(),
                TextColumn::make('category.name')->sortable()->searchable()->toggleable(),
                ColorColumn::make('color')->toggleable(),
                ImageColumn::make('thumbnail')->toggleable(),
                TextColumn::make('tags')->toggleable(),
                CheckboxColumn::make('published')->toggleable(),
                TextColumn::make('created_at')->label('Published on')->date()->sortable()->searchable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\CreateAction::make(),
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
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
