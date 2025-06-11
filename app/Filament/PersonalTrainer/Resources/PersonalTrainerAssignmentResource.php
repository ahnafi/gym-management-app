<?php

namespace App\Filament\PersonalTrainer\Resources;

use App\Filament\PersonalTrainer\Resources\PersonalTrainerAssignmentResource\Pages;
use App\Filament\PersonalTrainer\Resources\PersonalTrainerAssignmentResource\RelationManagers\PersonalTrainerSchedulesRelationManager;
use App\Filament\PersonalTrainer\Resources\PersonalTrainerScheduleRelationManagerResource\RelationManagers\PersonalTrainerAssignmentRelationManager;
use App\Models\PersonalTrainerAssignment;
use App\Models\User;
use App\Models\PersonalTrainerPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PersonalTrainerAssignmentResource extends Resource
{
    protected static ?string $model = PersonalTrainerAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Penugasan Personal Trainer';
    protected static ?string $modelLabel = 'Penugasan';
    protected static ?string $pluralModelLabel = 'Penugasan Personal Trainer';

    /**
     * Batasi data hanya untuk pelatih pribadi yang sedang login
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return parent::getEloquentQuery()
            ->where('personal_trainer_id', $user->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Anggota')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('personal_trainer_package_id')
                    ->label('Paket PT')
                    ->relationship('personalTrainerPackage', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),

                Forms\Components\TextInput::make('day_left')
                    ->label('Sisa Hari')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'paused' => 'Ditunda',
                        'completed' => 'Selesai',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable(),

                Tables\Columns\TextColumn::make('personalTrainerPackage.name')
                    ->label('Paket'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('day_left')
                    ->label('Sisa Hari'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'danger' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Aktif',
                        'paused' => 'Ditunda',
                        'completed' => 'Selesai',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Ubah'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PersonalTrainerSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalTrainerAssignments::route('/'),
            'create' => Pages\CreatePersonalTrainerAssignment::route('/buat'),
            'edit' => Pages\EditPersonalTrainerAssignment::route('/{record}/ubah'),
        ];
    }
}
