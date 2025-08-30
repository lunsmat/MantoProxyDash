<?php

namespace App\Filament\Resources\DeviceLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\DeviceLogs\Pages\ListDeviceLogs;
use App\Models\DeviceLog;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DeviceLogResource extends Resource
{
    protected static ?string $model = DeviceLog::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'device.name';

    protected static ?string $modelLabel = 'Log';

    protected static ?string $pluralModelLabel = 'Logs do acessos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device.mac_address')
                    ->searchable()
                    ->sortable()
                    ->label('Endereço MAC'),
                TextColumn::make('device.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome do Dispositivo'),
                TextColumn::make('http_method')
                    ->searchable()
                    ->sortable()
                    ->label('Método HTTP'),
                TextColumn::make('http_url')
                    ->searchable()
                    ->sortable()
                    ->label('URL HTTP'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado Em'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Atualizado Em'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
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
            'index' => ListDeviceLogs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }
}
