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
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceLogResource extends Resource
{
    protected static ?string $model = DeviceLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('Device Mac'),
                TextColumn::make('http_method')
                    ->searchable()
                    ->sortable()
                    ->label('HTTP Method'),
                TextColumn::make('http_url')
                    ->searchable()
                    ->sortable()
                    ->label('HTTP URL'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Updated At'),
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
            'index' => ListDeviceLogs::route('/'),
            'create' => CreateDeviceLog::route('/create'),
            'edit' => EditDeviceLog::route('/{record}/edit'),
        ];
    }
}
