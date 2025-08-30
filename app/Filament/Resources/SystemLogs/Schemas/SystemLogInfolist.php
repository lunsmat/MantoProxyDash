<?php

namespace App\Filament\Resources\SystemLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SystemLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID')
                    ->columnSpanFull(),
                TextEntry::make('message')
                    ->label('Mensagem')
                    ->columnSpanFull(),
                TextEntry::make('user_identifier')
                    ->label('Usuário'),
                TextEntry::make('object_identifier')
                    ->label('Objeto'),
                TextEntry::make('context')
                    ->label('Contexto')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Criado Em'),
            ]);
    }
}
