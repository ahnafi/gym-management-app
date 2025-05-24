<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymVisitResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Table;
use App\Models\GymVisit;

class GymVisitResource extends Resource
{
    protected static ?string $model = GymVisit::class;
    protected static ?string $label = 'Visit Gym';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?string $navigationGroup = 'Manajemen Penugasan dan Penjadwalan';
    protected static ?string $navigationLabel = 'Visit Gym';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGymVisits::route('/'),
            'create' => Pages\CreateGymVisit::route('/create'),
            'edit' => Pages\EditGymVisit::route('/{record}/edit'),
        ];
    }
}
