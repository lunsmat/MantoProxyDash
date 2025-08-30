<x-filament-panels::page>

    <header class="group-header">
        <h3>{{ $record->name }}</h3>

        <div class="headers-button">

            <x-filament::button wire:click="enableAll" class="activate">
                Habilitar Todos
            </x-filament::button>

            <x-filament::button class="deactivate" wire:click="disableAll">
                Desabilitar Todos
            </x-filament::button>

            <x-filament::modal id="schedule-deactivation-modal">
                <x-slot name="trigger">
                    <x-filament::button class="program">
                        Programar Desativação
                    </x-filament::button>
                </x-slot>

                <x-slot name="heading">
                    Programar Desativação
                </x-slot>

                @if (count($deactivations) > 0)
                <p class="your-programs">
                    Suas Programações
                </p>

                <ul>
                    @foreach ($deactivations as $deactivation)
                        <hr>
                        <li>
                            <p>Desativação: {{ $deactivation->reason }}</p>
                            De {{ $deactivation->deactivation_time_formatted }} a {{ $deactivation->reactivation_time_formatted }}
                        </li>
                    @endforeach
                    <hr >
                </ul>
                @endif

                <form wire:submit.prevent="scheduleDeactivation">
                    <div class="space-y-4">
                        <label>
                            <span>Data e Hora de Desativação</span>
                            <x-filament::input
                                type="datetime-local"
                                label="Data e Hora de Desativação"
                                wire:model.defer="deactivationDateTime"
                                required
                                style="margin-bottom: 1rem"
                            />
                        </label>

                        <label>
                            <span>Data e Hora da Reativação</span>
                            <x-filament::input
                                type="datetime-local"
                                label="Data e Hora da Reativação"
                                wire:model.defer="reactivationDateTime"
                                required
                                textarea
                                style="margin-bottom: 1rem"
                            />
                            @if ($deactivationFormError)
                                <small class="text-sm text-danger-600 mt-1">
                                    {{ $deactivationFormError }}
                                </small>
                                <br />
                                <br />
                            @endif
                        </label>

                        <label>
                            <span>Motivo da Desativação</span>
                            <x-filament::input
                                type="text"
                                label="Motivo da Desativação"
                                wire:model.defer="deactivationReason"
                                required
                                style="margin-bottom: 1rem"
                            />
                        </label>
                    </div>

                    <x-slot name="footerActions">
                        <x-filament::button wire:click="scheduleDeactivation">
                        Confirmar
                    </x-filament::button>
                </x-slot>
            </x-filament::modal>
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
                        <button wire:click="updateFilter({{ $filter }}, {{ in_array($filter->id, $enabledFilters) ? 'false' : 'true' }})" class="{{ in_array($filter->id, $enabledFilters) ? 'active' : '' }}">
                            {{ in_array($filter->id, $enabledFilters) ? 'Ativado' : 'Desativado' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</x-filament-panels::page>
