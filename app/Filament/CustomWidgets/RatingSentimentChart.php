<?php

namespace App\Filament\CustomWidgets;

use App\Models\EvaluationForm;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Str;
use Sentiment\Analyzer;

class RatingSentimentChart extends ApexChartWidget
{
    protected static ?string $pollingInterval = null;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'ratingSentimentChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Rating Sentiment Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $id = Str::afterLast(url()->current(), '/');

        $eval = EvaluationForm::where('activity_id', $id)->get();
        $list_eval = $eval->map(function ($record) {
            // Rating Sentiment

            $arr = explode(' / ', $record->overall_rating);
            if (($arr[0] / $arr[1]) <= (1 / 3)) {
                return ['type' => 'negative'];
            } else if (($arr[0] / $arr[1]) <= (2 / 3)) {
                return ['type' => 'neutral'];
            } else if (($arr[0] / $arr[1]) <= (3 / 3)) {
                return ['type' => 'positive'];
            }
        });
        $negative_total = $list_eval->filter(function ($value, $key) {
            return $value['type'] === 'negative';
        })->count();
        $positive_total = $list_eval->filter(function ($value, $key) {
            return $value['type'] === 'positive';
        })->count();
        $neutral_total = $list_eval->filter(function ($value, $key) {
            return $value['type'] === 'neutral';
        })->count();
        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => [$positive_total, $negative_total, $neutral_total],
            'labels' => ['Positive', 'Negative', 'Neutral',],
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}
