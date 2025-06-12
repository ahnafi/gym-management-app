<?php

namespace App\Filament\Resources\PersonalTrainerAssignmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalTrainerSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'personalTrainerSchedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('scheduled_at')
                    ->label('Tanggal Jadwal')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'scheduled' => 'Dijadwalkan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'missed' => 'Tidak Hadir',
                    ]),

                Forms\Components\TimePicker::make('check_in_time')
                    ->label('Waktu Masuk')
                    ->required(),

                Forms\Components\TimePicker::make('check_out_time')
                    ->label('Waktu Keluar'),

                Forms\Components\KeyValue::make('training_log')
                    ->label('Log Pelatihan')
                    ->keyLabel('Kunci')
                    ->valueLabel('Isi')
                    ->addActionLabel('Tambah Data'),

                Forms\Components\Textarea::make('trainer_notes')
                    ->label('Catatan Pelatih')
                    ->rows(3),

                Forms\Components\Textarea::make('member_feedback')
                    ->label('Masukan Member')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('scheduled_at')
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Tanggal Jadwal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'missed' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Masuk'),

                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('Keluar')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('trainer_notes')
                    ->label('Catatan Pelatih')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->trainer_notes),

                Tables\Columns\TextColumn::make('member_feedback')
                    ->label('Masukan Member')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->member_feedback),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Jadwal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Massal'),
                ]),
            ]);
    }
}
