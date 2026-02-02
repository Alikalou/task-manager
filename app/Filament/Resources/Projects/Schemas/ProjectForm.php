<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('todo'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
