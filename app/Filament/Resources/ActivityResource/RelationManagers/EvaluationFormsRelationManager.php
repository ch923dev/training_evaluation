<?php

namespace App\Filament\Resources\ActivityResource\RelationManagers;

use App\Models\EvaluationAnswer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Sentiment\Analyzer;

class EvaluationFormsRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluation_forms';

    protected static ?string $recordTitleAttribute = 'evaluatorName';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('evaluatorName')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluatorName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('overall_rating'),
                TextColumn::make('words_sentiment')
                    ->label('Words Sentiment')
                    ->formatStateUsing(function ($record) {
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

                        $pos_score = $total_pos_score;
                        $neg_score = $total_neg_score;
                        $neu_score = $total_neu_score;
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
                        return $words_score;
                    }),
                TextColumn::make('rating_sentiment')
                    ->label('Rating Sentiment')
                    ->formatStateUsing(function ($record) {
                        $words = $record->evaluation_answers->whereNotIn('answer', ['0', '1', '2', '3', '4', '5']);
                        // Rating Sentiment
                        $rating_score = '';
                        $arr =  explode(' / ', $record->overall_rating);
                        if (($arr[0] / $arr[1]) <= (1 / 3)) {
                            $rating_score =  'negative';
                        } else if (($arr[0] / $arr[1]) <= (2 / 3)) {
                            $rating_score =  'neutral';
                        } else if (($arr[0] / $arr[1]) <= (3 / 3)) {
                            $rating_score =  'positive';
                        }
                        return $rating_score;
                    }),
                TextColumn::make('sentiment')
                    ->label('Total Sentiment')

                    ->formatStateUsing(function ($record) {
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
                        $arr =  explode(' / ', $record->overall_rating);
                        if (($arr[0] / $arr[1]) <= (1 / 3)) {
                            $rating_score =  'negative';
                        } else if (($arr[0] / $arr[1]) <= (2 / 3)) {
                            $rating_score =  'neutral';
                        } else if (($arr[0] / $arr[1]) <= (3 / 3)) {
                            $rating_score =  'positive';
                        }
                        if ($words_score == 'positive' && $rating_score == 'positive') {
                            return 'positive';
                        } else if ($words_score == 'neutral' && $rating_score == 'positive') {
                            return 'positive';
                        } else if ($words_score == 'positive' && $rating_score == 'neutral') {
                            return 'positive';
                        } else if ($words_score == 'negative' && $rating_score == 'negative') {
                            return 'negative';
                        } else if ($words_score == 'neutral' && $rating_score == 'negative') {
                            return 'negative';
                        } else if ($words_score == 'negative' && $rating_score == 'neutral') {
                            return 'negative';
                        } else {
                            return 'neutral';
                        }
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
