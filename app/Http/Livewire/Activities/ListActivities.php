<?php

namespace App\Http\Livewire\Activities;

use Closure;
use Illuminate\Database\Eloquent\Model;
use App\Models\Activities;
use App\Models\Activity;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends Component implements HasTable
{
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Activity::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable(),
            TextColumn::make('venue'),
            TextColumn::make('facilitator'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            // ...
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // ...
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
    protected function getTableRecordUrlUsing(): Closure
    {

        // return fn(Model $record): string => route('evaluation-list');
        return fn(Activity $record): string => route('evaluation-form',['activity'=>$record]);
    }
}