<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommercialVehicleResource\Pages;
use App\Filament\Resources\CommercialVehicleResource\RelationManagers;
use App\Models\CommercialVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommercialVehicleResource extends Resource
{
    protected static ?string $model = CommercialVehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('plate_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vehicle_category_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('vehicle_manufacturer_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('vehicle_model_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('chassis_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('engine_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ticket_category_id')
                    ->numeric(),
                Forms\Components\TextInput::make('capacity')
                    ->maxLength(255),
                Forms\Components\TextInput::make('routes')
                    ->maxLength(255),
                Forms\Components\TextInput::make('driver_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('driver_license_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('driver_license_expiry_date')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('driver_license_image')
                    ->image(),
                Forms\Components\TextInput::make('permit_renewal_count')
                    ->numeric(),
                Forms\Components\TextInput::make('permit_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('permit_expiry_date')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('permit_image')
                    ->image(),
                Forms\Components\Textarea::make('note')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('added_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plate_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_category_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_manufacturer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle_model_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chassis_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('engine_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('routes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('driver_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('driver_license_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('driver_license_expiry_date')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('driver_license_image'),
                Tables\Columns\TextColumn::make('permit_renewal_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permit_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('permit_expiry_date')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('permit_image'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('added_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCommercialVehicles::route('/'),
            'create' => Pages\CreateCommercialVehicle::route('/create'),
            'edit' => Pages\EditCommercialVehicle::route('/{record}/edit'),
        ];
    }    
}
