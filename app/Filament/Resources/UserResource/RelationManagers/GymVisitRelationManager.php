<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GymVisitRelationManager extends RelationManager
{
    protected static ?string $label = 'Riwayat Kunjungan Gym';
    protected static string $relationship = 'gymVisits';
    protected static ?string $recordTitleAttribute = 'visit_date';

    protected function getTableHeading(): ?string
    {
        return 'Daftar Riwayat Visit Gym';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('visit_date')
                ->label('Tanggal Kunjungan')
                ->default(now())
                ->required(),

            TimePicker::make('entry_time')
                ->label('Jam Masuk')
                ->default(now()->format('H:i'))
                ->required(),

            TimePicker::make('exit_time')
                ->label('Jam Keluar')
                ->nullable(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'in_gym' => 'Masih di Gym',
                    'left' => 'Sudah Keluar',
                ])
                ->default('in_gym')
                ->required(),

            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('visit_date')->label('Tanggal'),
                TextColumn::make('entry_time')->label('Masuk'),
                TextColumn::make('exit_time')->label('Keluar')->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'primary' => 'in_gym',
                        'success' => 'left',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in_gym' => 'Masih di Gym',
                        'left' => 'Sudah Keluar',
                        default => ucfirst($state),
                    })
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
