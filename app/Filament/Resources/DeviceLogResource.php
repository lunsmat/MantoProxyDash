<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceLogResource\Pages;
use App\Filament\Resources\DeviceLogResource\RelationManagers;
use App\Models\DeviceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceLogResource extends Resource
{
    protected static ?string $model = DeviceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.mac_address')
                    ->searchable()
                    ->sortable()
                    ->label('Device Mac'),
                Tables\Columns\TextColumn::make('http_method')
                    ->searchable()
                    ->sortable()
                    ->label('HTTP Method'),
                Tables\Columns\TextColumn::make('http_url')
                    ->searchable()
                    ->sortable()
                    ->label('HTTP URL'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Updated At'),
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
            'index' => Pages\ListDeviceLogs::route('/'),
            'create' => Pages\CreateDeviceLog::route('/create'),
            'edit' => Pages\EditDeviceLog::route('/{record}/edit'),
        ];
    }
}
