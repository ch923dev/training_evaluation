<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageActivities extends ManageRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(User::find(auth()->user()->id)->role_id === Role::where('role', 'Admin')->first()->id)
                ->steps([
                    Step::make('Activity')
                        ->schema([
                            TextInput::make('title')
                                ->required(),
                            TextInput::make('venue')
                                ->required(),
                            TextInput::make('facilitator')
                                ->required(),
                            TextInput::make('Date')
                                ->required(),
                            TextInput::make('password')
                                ->type('password')
                                ->required(),
                        ]),
                    Step::make('Questions')
                        ->schema([
                            Repeater::make('Sections')
                                ->collapsible()
                                ->relationship('sections')
                                ->schema([
                                    TextInput::make('title'),
                                    Repeater::make('Questions')
                                        ->relationship('questions')
                                        ->schema([
                                            Card::make([
                                                TextInput::make('question')
                                                    ->required(),
                                                Select::make('type')
                                                    ->options([
                                                        'rating' => 'Rating',
                                                        'question' => 'Question'
                                                    ])
                                            ])->columns(2)
                                        ])
                                ])->createItemButtonLabel('Add New Section')
                        ])
                ]),
        ];
    }
}
