<?php

namespace App\Http\Livewire\Evaluation;

use App\Models\Activity;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationForm;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Livewire\Component;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;

class Form extends Component implements Forms\Contracts\HasForms

{
    use Forms\Concerns\InteractsWithForms;
    public Activity $activity;

    public $title = '';
    public $venue = '';
    public $facilitator = '';
    public $overall_rating = 0;
    public function mount(): void
    {
        $this->form->fill(
            [
                'title' => $this->activity->title,
                'created_at' => $this->activity->created_at,
                'venue' => $this->activity->venue,
                'facilitator' => $this->activity->facilitator,
                'sections' => $this->activity->sections,
            ]
        );
    }
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Title of Activity')
                ->disabled(),
            TextInput::make('created_at')
                ->label('Date of Activity')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('m/d/Y'))
                ->disabled(),
            TextInput::make('facilitator')
                ->label('Name of Speaker/Trainer/Facilitator')
                ->disabled(),
            TextInput::make('evaluator')
                ->label('Name of Evaluator'),
            TextInput::make('venue')
                ->label('Venue of Activity')
                ->disabled(),

            Card::make([
                Placeholder::make('')
                    ->content('5 - Excellent'),
                Placeholder::make('')
                    ->content('4 - Very Good'),
                Placeholder::make('')
                    ->content('3 - Good'),
                Placeholder::make('')
                    ->content('2 - Fair'),
                Placeholder::make('')
                    ->content('1 - Poor'),
                Placeholder::make('')
                    ->content('0 - Not Applicable'),
            ])->columns(3),
            Repeater::make('sections')
                ->extraAttributes(['class' => 'border-transparent border-0 color-white'])
                ->relationship('sections')
                ->disableItemDeletion()
                ->disableItemCreation()
                ->schema([
                    Placeholder::make('title')
                        ->extraAttributes(['class' => 'font-extrabold text-xl'])
                        ->content(fn ($record) => $record->title),
                    Repeater::make('questions')
                        ->label('')
                        ->disableItemDeletion()
                        ->disableItemCreation()
                        ->relationship('questions')
                        ->schema([
                            Group::make([
                                Placeholder::make('question')
                                    ->content(fn ($record) => $record->question)
                                    ->columnSpanFull(),
                                Placeholder::make('')
                                    ->columnSpan(1),
                                Radio::make('answers_rating')
                                    ->visible(fn ($record) => $record->type === 'rating')
                                    ->label('Rating')
                                    ->options([
                                        '0' => '0',
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                        '5' => '5',
                                    ])
                                    ->inline()
                                    ->columnSpan(2),
                            ])->columns(3),
                            TextInput::make('answers_questions')
                                ->label('Answer')
                                ->visible(fn ($record) => $record->type != 'rating')
                        ])
                ]),

            TextInput::make('remarks'),
        ];
    }
    public function submit() 
    {
        $total_rating = 0;
        foreach ($this->sections as $value) {
            foreach ($value['questions'] as $answers) {
                if (array_key_exists('answers_rating', $answers)) {
                    $total_rating = $total_rating + $answers['answers_rating'];
                }
            }
        }
        $overall_rating = $this->activity->questions->where('type', 'rating')->count() * 5;
        $eveluation = EvaluationForm::create([
            'activity_id' => $this->form->model->id,
            'evaluatorName' => $this->form->getLivewire()->evaluator,
            'remarks' => $this->form->getLivewire()->remarks,
            'overall_rating' => Arr::join([$total_rating, $overall_rating], ' / ')
        ]);
        foreach ($this->sections as $value) {
            foreach ($value['questions'] as $answers) {
                $answer = '';
                if ($answers['type'] == 'rating') {
                    $answer = $answers['answers_rating'];
                } else {
                    $answer = $answers['answers_questions'];
                }
                EvaluationAnswer::create(['evaluation_form_id' => $eveluation->id, 'question_id' => $answers['id'], 'answer' => $answer]);
            }
        }
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
        $this->form->fill([
            'title' => $this->activity->title,
            'created_at' => $this->activity->created_at,
            'venue' => $this->activity->venue,
            'facilitator' => $this->activity->facilitator,
            'sections' => $this->activity->sections,
            'remarks' => '',
            'evaluator' => '',
        ]);
        redirect(route('evaluation-list'));
    }
    protected function getFormModel(): Activity 
    {
        return $this->activity;
    }
    public function render() 
    {
        return view('livewire.evaluations.form');
    }

}