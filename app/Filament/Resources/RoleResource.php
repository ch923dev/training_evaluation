<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Users Management';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('role')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->formatStateUsing(fn ($state) => Str::ucfirst($state))
                    ->label('Role')
                    ->searchable(),
                TextColumn::make('users_count')
                    ->label('Total Users')
                    ->counts('users')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function canViewAny(): bool
    {
        return User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id;
    }
    public static function canCreate(): bool
    {
        return User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id;
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
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
