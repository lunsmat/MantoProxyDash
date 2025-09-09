<?php

namespace App\Filament\Resources\SSHUsers;

use App\Filament\Resources\SSHUsers\Pages\CreateSSHUsers;
use App\Filament\Resources\SSHUsers\Pages\EditSSHUsers;
use App\Filament\Resources\SSHUsers\Pages\ListSSHUsers;
use App\Filament\Resources\SSHUsers\Schemas\SSHUsersForm;
use App\Filament\Resources\SSHUsers\Tables\SSHUsersTable;
use App\Models\SSHUser;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SSHUsersResource extends Resource
{
    protected static ?string $model = SSHUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $modelLabel = 'Usuário SSH';

    protected static ?string $pluralModelLabel = 'Usuários SSH';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('username')
                ->required()
                ->maxLength(255)
                ->label('Nome de Usuário'),
            Select::make('authentication_method')
                ->options([
                    'password' => 'Senha',
                    'key' => 'Chave de Autenticação (Apenas RSA)',
                ])
                ->required()
                ->default('password')
                ->live()
                ->label('Método de Autenticação'),
            TextInput::make('password')
                ->maxLength(255)
                ->label('Senha')
                ->hidden(fn (callable $get) => $get('authentication_method') !== 'password'),
            FileUpload::make('public_key_file_path')
                ->label('Caminho da Chave Pública')
                ->directory('ssh_keys')
                ->visibility('private')
                // ->acceptedFileTypes(['.pub'])
                ->maxSize(1024)
                ->required(fn (callable $get) => $get('authentication_method') === 'key')
                ->hidden(fn (callable $get) => $get('authentication_method') !== 'key'),
            FileUpload::make('private_key_file_path')
                ->label('Caminho da Chave Privada')
                ->directory('ssh_keys')
                ->visibility('private')
                // ->acceptedFileTypes(['.pem', '.key'])
                ->maxSize(1024)
                ->required(fn (callable $get) => $get('authentication_method') === 'key')
                ->hidden(fn (callable $get) => $get('authentication_method') !== 'key'),
            TextInput::make('passphrase')
                ->maxLength(255)
                ->label('Frase Secreta')
                ->hidden(fn (callable $get) => $get('authentication_method') !== 'key'),
            TextInput::make('port')
                ->numeric()
                ->default(22)
                ->required()
                ->label('Porta'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return SSHUsersTable::configure($table);
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
            'index' => ListSSHUsers::route('/'),
            'create' => CreateSSHUsers::route('/create')
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }
}
