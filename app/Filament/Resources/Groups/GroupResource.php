<?php

namespace App\Filament\Resources\Groups;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Groups\Pages\ListGroups;
use App\Filament\Resources\Groups\Pages\CreateGroup;
use App\Filament\Resources\Groups\Pages\EditGroup;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Device;
use App\Models\Group;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ServerStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Grupo';

    protected static ?string $pluralModelLabel = 'Grupos';

    public static function form(Schema $schema): Schema
    {
        $devices = Device::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome do Grupo'),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->label('Descrição'),
                Select::make('devices')
                    ->multiple()
                    ->relationship('devices', 'name')
                    ->preload()
                    ->options($devices)
                    ->label('Dispositivos'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nome do Grupo')
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Descrição')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Criado Em')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Atualizado Em')
                    ->sortable(),
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
            'index' => ListGroups::route('/'),
            'create' => CreateGroup::route('/create'),
            'edit' => EditGroup::route('/{record}/edit'),
        ];
    }
}
