<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalTrainerAssignmentResource\Pages;
use App\Models\PersonalTrainerAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Set;
use App\Models\PersonalTrainerPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalTrainerAssignmentResource extends Resource
{
    protected static ?string $model = PersonalTrainerAssignment::class;
    protected static ?string $label = 'Penugasan Personal Trainer';
    protected static ?string $navigationGroup = 'Manajemen Penugasan dan Penjadwalan';
    protected static ?string $navigationLabel = 'Penugasan Personal Trainer';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('day_left')
                    ->numeric()
                    ->required()
                    ->label('Sisa Hari'),

                DateTimePicker::make('start_date')
                    ->required()
                    ->label('Tanggal Mulai'),

                DateTimePicker::make('end_date')
                    ->required()
                    ->label('Tanggal Selesai'),

                Select::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'cancelled' => 'Dibatalkan',
                        'completed' => 'Selesai',
                    ])
                    ->required()
                    ->default('active')
                    ->label('Status'),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Member')
                    ->required(),

                Select::make('personal_trainer_package_id')
                    ->label('Paket Trainer')
                    ->relationship('personalTrainerPackage', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $package = PersonalTrainerPackage::with('personalTrainer')->find($state);
                        if ($package && $package->personalTrainer) {
                            $set('personal_trainer_id', $package->personalTrainer->user_personal_trainer_id);
                        } else {
                            $set('personal_trainer_id', null);
                        }
                    }),

                Select::make('personal_trainer_id')
                    ->label('Personal Trainer')
                    ->options(function () {
                        return User::where('role', 'trainer')->pluck('name', 'id');
                    })
                    ->disabled()
                    ->dehydrated()
                    ->required(),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Member'),

                Tables\Columns\TextColumn::make('personalTrainer.nickname')
                    ->label('Nama Trainer'),

                Tables\Columns\TextColumn::make('personalTrainerPackage.name')
                    ->label('Nama Paket'),

                Tables\Columns\TextColumn::make('day_left')
                    ->label('Sisa Hari'),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->label('Tanggal Mulai'),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->label('Tanggal Selesai'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Ubah'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
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
            'index' => Pages\ListPersonalTrainerAssignments::route('/'),
            'create' => Pages\CreatePersonalTrainerAssignment::route('/create'),
            'edit' => Pages\EditPersonalTrainerAssignment::route('/{record}/edit'),
        ];
    }
}
