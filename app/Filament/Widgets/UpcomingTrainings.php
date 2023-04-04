<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\Widget;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTrainings extends BaseWidget
{
    protected static string $view = 'filament.widgets.upcoming-trainings';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Activity::whereDate('date','>',Carbon::today()->toDateString());
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable(),
            TextColumn::make('venue'),
            TextColumn::make('facilitator'),
            TextColumn::make('date')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('m-d-Y'))
        ];
    }
}
