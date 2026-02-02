<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create([
            'user_id'     => auth()->id(),
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
