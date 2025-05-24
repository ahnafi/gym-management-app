<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalTrainerPackageResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\PersonalTrainerPackage;

class PersonalTrainerPackageResource extends Resource
{
    protected static ?string $model = PersonalTrainerPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Manajemen Paket Gym';
    protected static ?string $navigationLabel = 'Paket Personal Trainer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Paket')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                TextInput::make('day_duration')
                    ->label('Durasi (dalam hari)')
                    ->numeric()
                    ->default(1),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Select::make('personal_trainer_id')
                    ->label('Personal Trainer')
                    ->relationship('personalTrainer', 'name', fn ($query) => $query->where('role', 'trainer'))
                    ->required(),

                FileUpload::make('images')
                    ->image()
                    ->directory('trainer-packages')
                    ->imagePreviewHeight('150'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Paket')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Paket')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('day_duration')
                    ->label('Durasi (dalam hari)'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),

                TextColumn::make('personalTrainer.name')
                    ->label('Trainer'),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalTrainerPackages::route('/'),
            'create' => Pages\CreatePersonalTrainerPackage::route('/create'),
            'edit' => Pages\EditPersonalTrainerPackage::route('/{record}/edit'),
        ];
    }
}
