<?php

namespace App\Filament\Resources\ClassSessions\Pages;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClassSession extends ViewRecord
{
    protected static string $resource = ClassSessionResource::class;

    public function getTitle(): string
    {
        return ClassSessionResource::getRecordTitle($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ClassSessionsTable::cancelAction(),
        ];
    }
}
