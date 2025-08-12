<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UrlFilterResource\Pages;
use App\Filament\Resources\UrlFilterResource\RelationManagers;
use App\Models\UrlFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UrlFilterResource extends Resource
{
    protected static ?string $model = UrlFilter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Filter Name'),
                Forms\Components\Textarea::make('filters')
                    ->required()
                    ->columnSpanFull()
                    ->rows(10)
                    ->label('Filters'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Filter Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUrlFilters::route('/'),
            'create' => Pages\CreateUrlFilter::route('/create'),
            'edit' => Pages\EditUrlFilter::route('/{record}/edit'),
        ];
    }
}
