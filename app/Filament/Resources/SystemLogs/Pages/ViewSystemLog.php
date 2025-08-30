<?php

namespace App\Filament\Resources\SystemLogs\Pages;

use App\Filament\Resources\SystemLogs\SystemLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemLog extends ViewRecord
{
    protected static string $resource = SystemLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user_identifier'] = $this->record->user_identifier;
        $data['object_identifier'] = $this->record->object_identifier;
        return $data;
    }
}
