<?php

namespace App\Filament\Admin\Resources\Produks\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GambarsRelationManager extends RelationManager
{
    protected static string $relationship = 'gambars';

    protected static ?string $title = 'Gambar Produk';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('url')
                ->label('Gambar')
                ->image()
                ->disk('public')
                ->directory('produk')
                ->imageEditor()
                ->maxSize(5120)
                ->required(),
            TextInput::make('alt')->label('Teks Alt')->maxLength(255),
            TextInput::make('urutan')->label('Urutan')->numeric()->default(0)->required(),
            Toggle::make('utama')->label('Gambar Utama'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('url')->label('Gambar')->disk('public')->square()->size(64),
                TextColumn::make('alt')->label('Teks Alt')->wrap(),
                TextColumn::make('urutan')->label('Urutan')->sortable(),
                IconColumn::make('utama')->label('Gambar Utama')->boolean(),
            ])
            ->reorderable('urutan')
            ->defaultSort('urutan')
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
