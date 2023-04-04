<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use App\Models\Role;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Webbingbrasil\FilamentDateFilter\DateFilter;



class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                Group::make([
                    TextInput::make('title'),
                    Group::make([
                        TextInput::make('venue'),
                        TextInput::make('facilitator'),
                    ])->columns(2),
                    TextInput::make('date')
                        ->label('Date of Training')
                        ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('m-d-Y')),
                    TextInput::make('key')

                ])->columns(1)
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('venue'),
                TextColumn::make('facilitator'),
                TextColumn::make('date')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('m-d-Y'))

            ])
            ->filters([
                DateFilter::make('date')
                    ->label(__('Created At'))
                    ->useColumn('date')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id),
                    Tables\Actions\DeleteAction::make()
                        ->visible(User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id),

            ]);
    }
    public static function canCreate(): bool
    {
        return User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id;
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\EvaluationFormsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }
}
