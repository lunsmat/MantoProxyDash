<x-filament-panels::page>

    <header class="group-header">
        <h3>{{ $record->name }}</h3>

        <div class="headers-button">
            <button wire:click="enableAll">Habilitar Todos</button>
            <button wire:click="disableAll" class="deactivate">Desabilitar Todos</button>
        </div>
    </header>

    <main class="group-template">
        <!-- First column:  -->
        <div class="devices-area">
            <h2 class="text-lg font-medium">Dispositivos</h2>
            <div class="devices-container">
                <!-- Container must devices with heroicon as a computer in gree with device allow connection true and red if false -->
                @foreach ($devices as $device)
                    <div
                        class="device-item {{ !$device['allow_connection'] ? 'disconnected' : '' }}"
                        wire:click="updateDeviceAuthorization({{ $device['id'] }}, {{  !($device['allow_connection']) ? 'true' : 'false' }})"
                    >
                        <x-filament::icon
                            alias="panels::topbar.global-search.field"
                            icon="heroicon-m-computer-desktop"
                            class="w-full"
                        />
                        <span>{{ $device['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="filters-area">
            <h2 class="text-lg font-medium">Filtros</h2>
            <div class="filters-container">
                @foreach ($filters as $filter)
                    <div class="filter">
                        <span>{{ $filter->name }}</span>
                        <button wire:click="updateFilter({{ $filter->id }}, {{ in_array($filter->id, $enabledFilters) ? 'false' : 'true' }})" class="{{ in_array($filter->id, $enabledFilters) ? 'active' : '' }}">
                            {{ in_array($filter->id, $enabledFilters) ? 'Ativado' : 'Desativado' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</x-filament-panels::page>
