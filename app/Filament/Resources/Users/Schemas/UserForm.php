<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email(),
                TextInput::make('password')
                    ->required(function (?string $context): bool {
                        return $context === 'create';
                    })
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->label('Senha'),
                TextInput::make('password_confirmation')
                    ->label('Confirmar Senha')
                    ->required(function (?string $context): bool {
                        return $context === 'create';
                    })
                    ->same('password')
                    ->password(),
                Select::make('role')
                    ->label('Cargo')
                    ->required()
                    ->options([
                        UserRole::User->getLabel() => 'Usuário',
                        UserRole::Admin->getLabel() => 'Admin',
                    ])
            ]);
    }
}
