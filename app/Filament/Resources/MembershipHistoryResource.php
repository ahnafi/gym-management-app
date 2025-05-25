<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipHistoryResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\MembershipHistory;

class MembershipHistoryResource extends Resource
{
    protected static ?string $model = MembershipHistory::class;
    protected static ?string $label = 'Riwayat Membership';
    protected static ?string $navigationGroup = 'Manajemen Penugasan dan Penjadwalan';
    protected static ?string $navigationLabel = 'Riwayat Membership';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?int $navigationSort = 7;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DateTimePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),

                DateTimePicker::make('end_date')
                    ->label('End Date')
                    ->required(),

                Select::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'expired' => 'Expired',
                    ])
                    ->label('Status')
                    ->required(),

                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Select::make('membership_package_id')
                    ->label('Membership Package')
                    ->relationship('membership_package', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('membership_package.name')
                    ->label('Membership Package')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'expired' => 'Expired',
                        default => ucfirst($state),
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListMembershipHistories::route('/'),
            'create' => Pages\CreateMembershipHistory::route('/create'),
            'edit' => Pages\EditMembershipHistory::route('/{record}/edit'),
        ];
    }
}
