<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipPackageResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\MembershipPackage;

class MembershipPackageResource extends Resource
{
    protected static ?string $model = MembershipPackage::class;
    protected static ?string $navigationGroup = 'Manajemen Paket Gym';
    protected static ?string $navigationLabel = 'Paket Membership';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Paket')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->required(),

                TextInput::make('duration')
                    ->label('Durasi (dalam hari)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->suffix('days'),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),

                FileUpload::make('images')
                    ->label('Gambar')
                    ->image()
                    ->directory('membership-packages')
                    ->imagePreviewHeight('150'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Paket')
                    ->sortable()
                    ->searchable(),


                TextColumn::make('name')
                    ->label('Nama Paket')
                    ->sortable()
                    ->searchable(),


                TextColumn::make('duration')
                    ->label('Durasi (dalam hari)'),


                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),


                TextColumn::make('status')->badge()->colors([
                    'success' => 'active',
                    'danger' => 'inactive',
                ]),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime(),
            ])
            ->filters([])
            ->actions([
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
            'index' => Pages\ListMembershipPackages::route('/'),
            'create' => Pages\CreateMembershipPackage::route('/create'),
            'edit' => Pages\EditMembershipPackage::route('/{record}/edit'),
        ];
    }
}
