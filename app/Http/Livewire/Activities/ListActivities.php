<?php

namespace App\Http\Livewire\Activities;

use Closure;
use Illuminate\Database\Eloquent\Model;
use App\Models\Activities;
use App\Models\Activity;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Webbingbrasil\FilamentDateFilter\DateFilter;

class ListActivities extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Activity::whereDate('date','=',Carbon::today()->toDateString());
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable(),
            TextColumn::make('venue'),
            TextColumn::make('facilitator'),
            TextColumn::make('date')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('m-d-Y')),
        ];
    }


    protected function getTableActions(): array
    {
        return [
            Action::make('evaluate')
                ->label('Evaluate')
                ->action(function ($record, array $data) {
                    if ($data['key'] !== $record->key) {
                        Notification::make('evaluation_key_error')
                            ->title('Evaluation Key Error')
                            ->body('You must have put an invalid key')
                            ->send();
                    } else {
                        redirect(route('evaluation-form', ['activity' => $record]));
                       
                    }
                })
                ->form([
                    TextInput::make('key')
                        ->required()
                ])
                
                ->button()

        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            // ...
        ];
    }

    public function render(): View
    {
        return view('livewire.activities.list-activities');
    }
    
}
