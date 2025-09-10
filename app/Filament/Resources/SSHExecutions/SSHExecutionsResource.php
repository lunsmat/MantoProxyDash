<?php

namespace App\Filament\Resources\SSHExecutions;

use App\Filament\Resources\SSHExecutions\Pages\CreateSSHExecutions;
use App\Filament\Resources\SSHExecutions\Pages\EditSSHExecutions;
use App\Filament\Resources\SSHExecutions\Pages\ListSSHExecutions;
use App\Filament\Resources\SSHExecutions\Schemas\SSHExecutionsForm;
use App\Filament\Resources\SSHExecutions\Tables\SSHExecutionsTable;
use App\Models\Device;
use App\Models\Group;
use App\Models\SSHExecution;
use App\Models\SSHUser;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SSHExecutionsResource extends Resource
{
    protected static ?string $model = SSHExecution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CodeBracketSquare;

    protected static ?string $modelLabel = 'Execução de Script SSH';

    protected static ?string $pluralModelLabel = 'Execuções de Scripts SSH';

    public static function form(Schema $schema): Schema
    {
        $devices = Device::all()->pluck('name', 'id')->toArray();
        $groups = Group::all()->pluck('name', 'id')->toArray();
        $sshUsers = SSHUser::all()->pluck('username', 'id')->toArray();

        return $schema->components([
            Select::make('run_type')
                ->options([
                    'command' => 'Commando',
                    'script' => 'Script',
                ])
                ->required()
                ->default('command')
                ->live()
                ->hidden(fn (?Model $record) => $record !== null)
                ->label('Tipo de Execução'),
            Textarea::make('command')
                ->label('Comando')
                ->rows(7)
                ->columnSpanFull()
                ->required(fn (callable $get) => $get('run_type') === 'command')
                ->hidden(fn (callable $get, ?Model $record) => $record !== null && $record->run_type !== 'command' || $record === null && $get('run_type') !== 'command'),
            FileUpload::make('script_path')
                ->label('Script')
                ->directory('ssh_scripts')
                ->visibility('private')
                ->maxSize(2048)
                ->required(fn (callable $get) => $get('run_type') === 'script')
                ->hidden(fn (callable $get, ?Model $record) => $record !== null && $record->run_type !== 'script' || $record === null && $get('run_type') !== 'script'),
            Select::make('object_type')
                ->label('Tipo de Objeto')
                ->options([
                    Device::class => 'Dispositivo',
                    Group::class => 'Grupo',
                ])
                ->live()
                ->required()
                ->hidden(fn (?Model $record) => $record !== null)
                ->default(Device::class),
            Select::make('object_id')
                ->label('Objeto')
                ->options(function (callable $get) use ($devices, $groups) {
                    $type = $get('object_type') ?? Device::class;
                    if ($type === Device::class) {
                        return $devices;
                    } elseif ($type === Group::class) {
                        return $groups;
                    }
                    return [];
                })->required()
                ->searchable()
                ->hidden(fn (?Model $record) => $record !== null),
            Select::make('ssh_user_id')
                ->label('Usuário SSH')
                ->options($sshUsers)
                ->required()
                ->searchable()
                ->hidden(fn (?Model $record) => $record !== null)
                ->default(array_key_first($sshUsers)),
            TextArea::make('output')
                ->label('Output')
                ->rows(15)
                ->columnSpanFull()
                ->disabled()
                ->hidden(fn (?Model $record) => $record === null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return SSHExecutionsTable::configure($table);
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
            'index' => ListSSHExecutions::route('/'),
            'create' => CreateSSHExecutions::route('/create'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->is_admin;
    }

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }
}
