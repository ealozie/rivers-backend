<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('log_date')
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('payer_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payer_phone_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payer_address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('receipt_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('method')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_channel')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_channel_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('deposit_slip_number')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('cheque_value_date'),
                Forms\Components\TextInput::make('bank_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_id')
                    ->numeric(),
                Forms\Components\TextInput::make('product_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_type_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('branch_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('branch_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payer_phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payer_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receipt_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_channel_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deposit_slip_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cheque_value_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_type_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch_name')
                    ->searchable(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }    
}
