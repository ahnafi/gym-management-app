<?php

namespace App\Filament\Resources\GymClassResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class GymClassSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'gymClassSchedules';
    protected static ?string $label = 'Jadwal Kelas';

    protected function getTableHeading(): ?string
    {
        return 'Daftar Jadwal Kelas';
    }

    protected static ?string $recordTitleAttribute = 'date';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),

                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->format('24hr'),

                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->format('24hr'),

                TextInput::make('slot')
                    ->label('Slot Peserta')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time(),

                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time(),

                TextColumn::make('slot')
                    ->label('Slot')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
