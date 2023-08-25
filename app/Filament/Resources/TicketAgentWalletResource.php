<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketAgentWalletResource\Pages;
use App\Filament\Resources\TicketAgentWalletResource\RelationManagers;
use App\Models\TicketAgentWallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketAgentWalletResource extends Resource
{
    protected static ?string $model = TicketAgentWallet::class;
    protected static ?string $navigationGroup = 'Ticketing';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_agent_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('transaction_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_reference_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('transaction_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('added_by')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_agent_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_status')
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
            'index' => Pages\ListTicketAgentWallets::route('/'),
            'create' => Pages\CreateTicketAgentWallet::route('/create'),
            'edit' => Pages\EditTicketAgentWallet::route('/{record}/edit'),
        ];
    }
}
