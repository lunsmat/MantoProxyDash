<?php

namespace App\Filament\Resources\SSHUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SSHUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('port')->label('Porta')->sortable()->searchable(),
                TextColumn::make('username')->label('Usuário')->sortable()->searchable(),
                TextColumn::make('authentication_method')->label('Método de Autenticação')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable()->searchable(),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable()->searchable(),
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
}
