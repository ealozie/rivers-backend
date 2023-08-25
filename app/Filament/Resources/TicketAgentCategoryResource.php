<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketAgentCategoryResource\Pages;
use App\Filament\Resources\TicketAgentCategoryResource\RelationManagers;
use App\Models\TicketAgentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketAgentCategoryResource extends Resource
{
    protected static ?string $model = TicketAgentCategory::class;

    protected static ?string $navigationGroup = 'Ticketing';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_agent_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ticket_category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount')
                    ->numeric(),
                Forms\Components\TextInput::make('added_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_agent_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket_category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('added_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListTicketAgentCategories::route('/'),
            'create' => Pages\CreateTicketAgentCategory::route('/create'),
            'edit' => Pages\EditTicketAgentCategory::route('/{record}/edit'),
        ];
    }
}
