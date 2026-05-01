<?php

namespace App\Filament\Admin\Resources\BlogPosts;

use App\Filament\Admin\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Admin\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Admin\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Admin\Resources\BlogPosts\Schemas\BlogPostForm;
use App\Filament\Admin\Resources\BlogPosts\Tables\BlogPostsTable;
use App\Models\BlogPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-newspaper';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return BlogPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogPostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
