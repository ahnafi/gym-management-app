<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembershipHistoriesRelationManager extends RelationManager
{
    protected static ?string $label = 'Riwayat Membership';
    protected static string $relationship = 'membershipHistories';
    protected static ?string $recordTitleAttribute = 'start_date';

    protected function getTableHeading(): ?string
    {
        return 'Daftar Riwayat Membership';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DateTimePicker::make('start_date')
                    ->required()
                    ->label('Start Date'),
                DateTimePicker::make('end_date')
                    ->required()
                    ->label('End Date'),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                    ])
                    ->default('active'),
                Select::make('membership_package_id')
                    ->relationship('membership_package', 'name')
                    ->required()
                    ->label('Membership Package'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('start_date')->dateTime()->label('Start Date'),
                TextColumn::make('end_date')->dateTime()->label('End Date'),
                TextColumn::make('status')->label('Status'),
                TextColumn::make('membership_package.name')->label('Membership Package'),
                TextColumn::make('deleted_at')->dateTime()->label('Deleted At')->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
