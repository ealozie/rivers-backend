<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketAgentResource\Pages;
use App\Filament\Resources\TicketAgentResource\RelationManagers;
use App\Models\TicketAgent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketAgentResource extends Resource
{
    protected static ?string $model = TicketAgent::class;
    protected static ?string $navigationGroup = 'Ticketing';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('agent_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('agent_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('discount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('added_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('wallet_balance')
                    ->required()
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
                Tables\Columns\TextColumn::make('agent_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agent_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('added_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wallet_balance')
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
            'index' => Pages\ListTicketAgents::route('/'),
            'create' => Pages\CreateTicketAgent::route('/create'),
            'edit' => Pages\EditTicketAgent::route('/{record}/edit'),
        ];
    }
}
