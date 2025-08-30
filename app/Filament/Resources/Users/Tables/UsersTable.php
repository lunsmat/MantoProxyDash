<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Services\UserService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('role_label')
                    ->label('Cargo'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->before(function (mixed $records) {
                        $service = new UserService();
                        foreach ($records as $record) {
                            $service->registerLog($record, "Usuário excluído", [
                                'user_id' => Auth::user()?->id,
                                'user' => $record->toArray(),
                            ]);
                        }
                    }),
                ]),
            ]);
    }
}
