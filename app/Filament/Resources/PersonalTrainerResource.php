<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalTrainerResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\PersonalTrainer;

class PersonalTrainerResource extends Resource
{
    protected static ?string $model = PersonalTrainer::class;

    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $navigationLabel = 'Personal Trainer';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nickname')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                Select::make('user_personal_trainer_id')
                    ->label('Personal Trainer')
                    ->relationship('userPersonalTrainer', 'name')
                    ->required(),

                FileUpload::make('images')
                    ->label('Gambar')
                    ->image()
                    ->directory('personal-trainers')
                    ->imagePreviewHeight('150'),

                TextInput::make('metadata')
                    ->label('Metadata')
                    ->json()
                    ->required()
                    ->maxLength(65535)
                    ->placeholder('{"key": "value"}')
                    ->helperText('Masukkan metadata dalam format JSON. Contoh: {"key": "value"}'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nickname')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),

                TextColumn::make('userPersonalTrainer.name')
                    ->label('Personal Trainer'),

                TextColumn::make('metadata')
                    ->label('Metadata')
                    ->limit(30),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPersonalTrainers::route('/'),
            'create' => Pages\CreatePersonalTrainer::route('/create'),
            'edit' => Pages\EditPersonalTrainer::route('/{record}/edit'),
        ];
    }
}
