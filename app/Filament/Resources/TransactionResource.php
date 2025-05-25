<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\PersonalTrainerPackage;
use App\Models\GymClass;
use App\Models\MembershipPackage;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $label = 'Transaksi Pengguna';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Transaksi Pengguna';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Kode Pembayaran')
                    ->disabled()
                    ->dehydrated(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Member')
                    ->required(),

                Select::make('purchasable_type')
                    ->label('Tipe Pembelian')
                    ->options([
                        'membership_package' => 'Membership Package',
                        'gym_class' => 'Gym Class',
                        'personal_trainer_package' => 'Personal Trainer Package',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('purchasable_id', null)),

                Select::make('purchasable_id')
                    ->label('Pilih Paket / Kelas')
                    ->options(function (callable $get) {
                        return match ($get('purchasable_type')) {
                            'membership_package' => MembershipPackage::all()->pluck('name', 'id'),
                            'gym_class' => GymClass::all()->pluck('name', 'id'),
                            'personal_trainer_package' => PersonalTrainerPackage::all()->pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (callable $get) => blank($get('purchasable_type')))
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $type = $get('purchasable_type');
                        $id = $get('purchasable_id');

                        $price = match ($type) {
                            'membership_package' => MembershipPackage::find($id)?->price,
                            'gym_class' => GymClass::find($id)?->price,
                            'personal_trainer_package' => PersonalTrainerPackage::find($id)?->price,
                            default => 0,
                        };

                        $set('amount', $price ?? 0);
                    }),

                TextInput::make('amount')
                    ->label('Nominal Pembayaran')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),

                DatePicker::make('gym_class_date')
                    ->label('Tanggal Kelas Gym')
                    ->nullable(),

                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->label('Status Pembayaran')
                    ->required(),

                DateTimePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('purchasable_type')
                    ->label('Tipe Pembelian')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'membership_package' => 'Paket Membership',
                        'gym_class' => 'Kelas Gym',
                        'personal_trainer_package' => 'Paket Personal Trainer',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('gym_class_date')
                    ->label('Tanggal Kelas Gym')
                    ->formatStateUsing(fn($state) => $state ? $state->format('Y-m-d') : '-'),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Nominal Pembayaran')
                    ->formatStateUsing(fn($state) => $state ?? '-'),

                TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                        'failed' => 'Gagal',
                        default => ucfirst($state),
                    })

                    ->label('Status'),

                TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->formatStateUsing(fn($state) => $state ? $state->format('Y-m-d H:i:s') : '-')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->label('Filter Status'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
