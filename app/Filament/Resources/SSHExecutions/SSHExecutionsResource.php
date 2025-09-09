<?php

namespace App\Filament\Resources\SSHExecutions;

use App\Filament\Resources\SSHExecutions\Pages\CreateSSHExecutions;
use App\Filament\Resources\SSHExecutions\Pages\EditSSHExecutions;
use App\Filament\Resources\SSHExecutions\Pages\ListSSHExecutions;
use App\Filament\Resources\SSHExecutions\Schemas\SSHExecutionsForm;
use App\Filament\Resources\SSHExecutions\Tables\SSHExecutionsTable;
use App\Models\SSHExecution;
use BackedEnum;
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
        return SSHExecutionsForm::configure($schema);
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
            'edit' => EditSSHExecutions::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->is_admin;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
