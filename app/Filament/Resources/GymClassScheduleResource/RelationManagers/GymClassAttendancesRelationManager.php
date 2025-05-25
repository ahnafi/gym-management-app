<?php

namespace App\Filament\Resources\GymClassScheduleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;

class GymClassAttendancesRelationManager extends RelationManager
{
    protected static ?string $label = 'Kehadiran Kelas Gym';
    protected static string $relationship = 'gymClassAttendances';
    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Member')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                DateTimePicker::make('attended_at')
                    ->label('Waktu Presensi'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'assigned' => 'Dijadwalkan',
                        'attended' => 'Hadir',
                        'missed' => 'Tidak Hadir',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attended_at')
                    ->label('Presensi')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
