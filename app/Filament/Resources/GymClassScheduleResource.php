<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymClassScheduleResource\Pages;
use App\Filament\Resources\GymClassScheduleResource\RelationManagers\GymClassAttendancesRelationManager;
use App\Models\GymClassSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class GymClassScheduleResource extends Resource
{
    protected static ?string $model = GymClassSchedule::class;
    protected static ?string $label = 'Jadwal Kelas Gym';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Manajemen Penugasan dan Penjadwalan';
    protected static ?string $navigationLabel = 'Jadwal Kelas Gym';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('gym_class_id')
                    ->label('Kelas Gym')
                    ->relationship('gymClass', 'name')
                    ->required()
                    ->searchable(),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),

                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required(),

                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required(),

                TextInput::make('slot')
                    ->label('Slot Peserta')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gymClass.name')
                    ->label('Kelas Gym')
                    ->searchable()
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('gym_class_id')
                    ->label('Kelas Gym')
                    ->relationship('gymClass', 'name'),
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
            GymClassAttendancesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymClassSchedules::route('/'),
            'create' => Pages\CreateGymClassSchedule::route('/create'),
            'edit' => Pages\EditGymClassSchedule::route('/{record}/edit'),
        ];
    }
}
