<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalTrainerResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\KeyValue;
use App\Models\PersonalTrainer;

class PersonalTrainerResource extends Resource
{
    protected static ?string $model = PersonalTrainer::class;
    protected static ?string $label = 'Personal Trainer';
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
                    ->searchable()
                    ->required(),

                FileUpload::make('images')
                    ->label('Gambar')
                    ->image()
                    ->multiple()
                    ->directory('personal-trainers')
                    ->imagePreviewHeight('150'),

                KeyValue::make('metadata')
                    ->label('Metadata')
                    ->keyLabel('Kunci')
                    ->valueLabel('Isi')
                    ->addActionLabel('Tambah Data')
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
                    ->formatStateUsing(function ($state, $record) {
                        $decoded = $record->metadata; // use casted model field

                        if (!is_array($decoded)) return '-';

                        return collect($decoded)
                            ->map(fn($value, $key) => "<strong>{$key}</strong>: {$value}")
                            ->implode('<br>');
                    })
                    ->html()
                    ->wrap()
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
