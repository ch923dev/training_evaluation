<?php

namespace App\Filament\CustomWidgets;

use App\Models\EvaluationForm;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Str;
use Sentiment\Analyzer;

class WordsSentimentChart extends ApexChartWidget
{
    protected static ?string $pollingInterval = null;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'wordsSentimentChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Words Sentiment Chart';

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
            $words = $record->evaluation_answers->whereNotIn('answer', ['0', '1', '2', '3', '4', '5']);
            // Words Sentiment
            $words_score = '';
            $sentiment = new Analyzer();
            $total_neg_score = 0;
            $total_pos_score = 0;
            $total_neu_score = 0;
            foreach ($words as $word) {
                $score = $sentiment->getSentiment($word->answer);
                $total_neg_score = $total_neg_score + $score['neg'];
                $total_pos_score = $total_pos_score + $score['pos'];
                $total_neu_score = $total_neu_score + $score['neu'];
            }

            $pos_score = $total_pos_score / $words->count();
            $neg_score = $total_neg_score / $words->count();
            $neu_score = $total_neu_score / $words->count();
            switch (max([$pos_score, $neg_score, $neu_score])) {
                case $pos_score:
                    return ['type' => 'positive'];

                case $neg_score:
                    return ['type' => 'negative'];

                case $neu_score:
                    return ['type' => 'neutral'];
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
