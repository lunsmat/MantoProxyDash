<?php

namespace App\Filament\Resources\Devices;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Devices\Pages\ListDevices;
use App\Filament\Resources\Devices\Pages\CreateDevice;
use App\Filament\Resources\Devices\Pages\EditDevice;
use App\Models\Device;
use App\Models\Group;
use App\Services\DeviceService;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ComputerDesktop;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Dispositivo';

    protected static ?string $pluralModelLabel = 'Dispositivos';

    private DeviceService $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }


    public static function form(Schema $schema): Schema
    {
        $groups = Group::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nome do Dispositivo'),
                TextInput::make('mac_address')
                    ->required()
                    ->maxLength(17)
                    ->helperText('Formato: XX-XX-XX-XX-XX-XX')
                    ->mask('**-**-**-**-**-**')
                    ->label('Endereço MAC')
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                Toggle::make('allow_connection')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->default(true)
                    ->label('Permitir Conexão'),

                Select::make('groups')
                    ->multiple()
                    ->relationship('groups', 'name')
                    ->preload()
                    ->options($groups)
                    ->label('Grupos'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome do Dispositivo'),
                TextColumn::make('mac_address')
                    ->searchable()
                    ->sortable()
                    ->label('Endereço MAC'),
                ToggleColumn::make('allow_connection')
                    ->label('Permitir Conexão')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->action(function ($record, $state) {
                        $this->deviceService->updateConnectionState($record, $state);
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Criado Em'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Atualizado Em'),
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
                        $service = new DeviceService();

                        foreach ($records as $record) {
                            $record->load(['groups', 'filters']);
                            $data = [
                                'user_id' => auth()->id(),
                                'device_id' => $record->toArray(),
                            ];
                            $service->registerLog($record, "Dispositivo removido", $data);
                        }
                    }),
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
            'index' => ListDevices::route('/'),
            'create' => CreateDevice::route('/create'),
            'edit' => EditDevice::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->is_admin;
    }
}
