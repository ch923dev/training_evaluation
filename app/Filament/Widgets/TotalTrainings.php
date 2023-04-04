<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\EvaluationForm;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TotalTrainings extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Trainings', Activity::count()),
            Card::make('Total Evaluators', EvaluationForm::count()),
            Card::make('Upcoming Trainings', Activity::whereDate('date', '>', (Carbon::today()->toDateString()))->count()),
        ];
    }
}
