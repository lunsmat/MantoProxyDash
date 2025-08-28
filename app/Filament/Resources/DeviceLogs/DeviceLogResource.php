<?php

namespace App\Filament\Resources\DeviceLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DeviceLogs\Pages\ListDeviceLogs;
use App\Filament\Resources\DeviceLogs\Pages\CreateDeviceLog;
use App\Filament\Resources\DeviceLogs\Pages\EditDeviceLog;
use App\Filament\Resources\DeviceLogResource\Pages;
use App\Filament\Resources\DeviceLogResource\RelationManagers;
use App\Models\DeviceLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     // DeleteBulkAction::make(),
                // ]),
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
            // 'create' => CreateDeviceLog::route('/create'),
            // 'edit' => EditDeviceLog::route('/{record}/edit'),
        ];
    }
}
