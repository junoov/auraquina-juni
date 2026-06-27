<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Peran')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Peran')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(125),
                    TextInput::make('guard_name')
                        ->label('Guard Akses')
                        ->default('web')
                        ->required(),
                ]),
            Section::make('Hak Akses')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Hak Akses')
                        ->relationship('permissions', 'name')
                        ->options(fn () => Permission::pluck('name', 'id'))
                        ->columns(3)
                        ->bulkToggleable()
                        ->searchable(),
                ]),
        ]);
    }
}
