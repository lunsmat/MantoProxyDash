<?php

namespace App\Filament\Resources\SystemLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('message')->label('Mensagem')->sortable()->searchable(),
                TextColumn::make('user_identifier')->label('Usuário')->sortable()->searchable(),
                TextColumn::make('object_identifier')->label('Objeto')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Criado Em')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('id', 'desc');
    }
}
