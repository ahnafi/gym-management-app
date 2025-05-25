<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionRelationManager extends RelationManager
{
    protected static ?string $label = 'Riwayat Transaksi';
    protected static ?string $recordTitleAttribute = 'code';


    protected static string $relationship = 'transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Kode Pembayaran')
                    ->disabled(),

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')->label('Kode Pembayaran')->searchable()->sortable(),

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
                    ->date()
                    ->formatStateUsing(fn($state) => $state ?? '-'),

                TextColumn::make('amount')
                    ->label('Nominal Pembayaran')
                    ->money('IDR'),

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
                    ->dateTime()
                    ->formatStateUsing(fn($state) => $state ?? '-'),
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
