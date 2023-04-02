<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use App\Filament\CustomWidgets\BlogPostsChart;
use App\Filament\CustomWidgets\WordsSentimentChart;
use App\Filament\CustomWidgets\RatingSentimentChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()->hidden(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BlogPostsChart::class,
            WordsSentimentChart::class,
            RatingSentimentChart::class
        ];
    }
    protected function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
