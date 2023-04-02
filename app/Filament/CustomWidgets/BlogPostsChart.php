<?php

namespace App\Filament\CustomWidgets;

use App\Models\Activity;
use App\Models\EvaluationForm;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Str;
use Sentiment\Analyzer;

class BlogPostsChart extends ApexChartWidget
{
    protected static ?string $pollingInterval = null;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'blogPostsChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total Sentiments';

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
                    $words_score = 'positive';
                    break;
                case $neg_score:
                    $words_score = 'negative';
                    break;
                case $neu_score:
                    $words_score = 'neutral';
                    break;
            }


            // Rating Sentiment
            $rating_score = '';
            $arr = explode(' / ', $record->overall_rating);
            if (($arr[0] / $arr[1]) <= (1 / 3)) {
                $rating_score = 'negative';
            } else if (($arr[0] / $arr[1]) <= (2 / 3)) {
                $rating_score = 'neutral';
            } else if (($arr[0] / $arr[1]) <= (3 / 3)) {
                $rating_score = 'positive';
            }
            if ($words_score == 'positive' && $rating_score == 'positive') {
                return ['type' => 'positive'];
            } else if ($words_score == 'neutral' && $rating_score == 'positive') {
                return ['type' => 'positive'];
            } else if ($words_score == 'positive' && $rating_score == 'neutral') {
                return ['type' => 'positive'];
            } else if ($words_score == 'negative' && $rating_score == 'negative') {
                return ['type' => 'negative'];
            } else if ($words_score == 'neutral' && $rating_score == 'negative') {
                return ['type' => 'negative'];
            } else if ($words_score == 'negative' && $rating_score == 'neutral') {
                return ['type' => 'negative'];
            } else {
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
