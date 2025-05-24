<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymClassResource\Pages;
use App\Filament\Resources\GymClassResource\RelationManagers\GymClassSchedulesRelationManager;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\GymClass;

class GymClassResource extends Resource
{
    protected static ?string $model = GymClass::class;
    protected static ?string $label = 'Kelas Gym';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Manajemen Paket Gym';
    protected static ?string $navigationLabel = 'Kelas Gym';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                FileUpload::make('images')
                    ->label('Gambar')
                    ->image()
                    ->directory('gym-classes')
                    ->imagePreviewHeight('150'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Kelas')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GymClassSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymClasses::route('/'),
            'create' => Pages\CreateGymClass::route('/create'),
            'edit' => Pages\EditGymClass::route('/{record}/edit'),
        ];
    }
}
