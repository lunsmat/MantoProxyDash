<?php

namespace App\Filament\Resources\UrlFilters;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UrlFilters\Pages\ListUrlFilters;
use App\Filament\Resources\UrlFilters\Pages\CreateUrlFilter;
use App\Filament\Resources\UrlFilters\Pages\EditUrlFilter;
use App\Filament\Resources\UrlFilterResource\Pages;
use App\Filament\Resources\UrlFilterResource\RelationManagers;
use App\Models\UrlFilter;
use Filament\Forms;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UrlFilterResource extends Resource
{
    protected static ?string $model = UrlFilter::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $modelLabel = 'Filtro';

    protected static ?string $pluralModelLabel = 'Filtros';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nome do Filtro'),
                CodeEditor::make('filters')
                    ->required()
                    ->language(Language::Yaml)
                    ->columnSpanFull()
                    ->label('Filtros'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome do Filtro'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListUrlFilters::route('/'),
            'create' => CreateUrlFilter::route('/create'),
            'edit' => EditUrlFilter::route('/{record}/edit'),
        ];
    }
}
